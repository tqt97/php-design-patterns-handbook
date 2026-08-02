# Module 38 — Production: Command

## Vì sao bài này tồn tại?

**Command bus đa worker** là tình huống độc lập được xây dựng riêng cho Command. Bài không lấy từ một source ẩn: bạn tự tạo phiên bản `before` tối thiểu dựa trên mô tả dưới đây, sau đó dùng test để chứng minh refactor không đổi behavior. Bài Production giả định hệ thống đã chạy thật. Ngoài cấu trúc code, lời giải phải xử lý migration, failure, idempotency hoặc observability phù hợp với use case.

## Câu chuyện nghiệp vụ

Hệ thống cần xử lý **Command bus đa worker**. `DistributedCommandBus` đang dispatch payload không version và không có idempotency, nên duplicate delivery có thể lặp side effect.

Invariant trung tâm của bài **Command** là:

> **mỗi command có handler duy nhất và idempotency key.**

Ở cấp Production, **Command** phải bảo vệ invariant dưới retry/concurrency hoặc partial failure, đồng thời có migration seam, telemetry, rollback trigger và cleanup condition sau rollout.

Failure bắt buộc phải được mô hình hóa:

> **duplicate delivery hoặc handler version mismatch.**

## Trạng thái code ban đầu

```php
final class DistributedCommandBus
{
    public function handle(array $input): mixed
    {
        // validation chung, lựa chọn biến thể và side effect
        // đang bị trộn trong một method.
        throw new RuntimeException('Hãy dựng phiên bản before phù hợp đề bài.');
    }
}
```

Đây là skeleton định hướng, không phải lời giải. Hãy thêm dữ liệu và behavior nhỏ nhất đủ tái hiện pain point của **Command bus đa worker**.

## Mô hình thiết kế cần hướng tới

```mermaid
sequenceDiagram
    participant API as API
    participant B as CommandBus
    participant Q as Command Queue
    participant H as CommandHandler
    participant I as Idempotency Store
    API->>B: dispatch(commandId, payloadVersion)
    B->>Q: enqueue envelope
    Q->>H: deliver attempt
    H->>I: claim commandId
    alt first execution
      H->>H: execute use case
      H->>I: store result
    else duplicate
      I-->>H: previous result
    end
```

Command envelope cần id, type, version, correlation và causation. Handler phải idempotent; ordering/concurrency được định nghĩa theo aggregate key thay vì giả định queue luôn tuần tự.

## Nhiệm vụ

1. Khóa behavior hiện tại của **Command bus đa worker** bằng characterization test và log một trace hoàn chỉnh.
2. Xác định source of truth, transaction boundary và side effect bên ngoài quanh `CommandBus`.
3. Tạo một migration seam để chạy song song implementation cũ/mới; so sánh kết quả trước khi chuyển traffic.
4. Mô phỏng failure **duplicate delivery hoặc handler version mismatch** và chứng minh retry/replay không phá invariant.
5. Bổ sung inbox, retry classification và trace command lifecycle; định nghĩa metric, alert và rollback trigger.
6. Viết ADR ghi rõ evidence, phương án baseline, cleanup condition và người sở hữu runbook.

## Dữ liệu thử tối thiểu

- Hai scenario hợp lệ đại diện cho hai biến thể khác nhau.
- Một boundary value liên quan trực tiếp tới **mỗi command có handler duy nhất và idempotency key**.
- Một scenario tạo ra **duplicate delivery hoặc handler version mismatch**.
- Một operation lặp lại và một scenario concurrent/replay.

## Gợi ý triển khai

- Bắt đầu bằng behavior và invariant; chỉ tạo abstraction sau khi xác định đúng trục thay đổi.
- Đặt tên method theo domain của **Command bus đa worker**, tránh `execute(mixed $data)` hoặc `handleAnything()`.
- Tách selection/wiring khỏi business calculation; composition root có thể biết concrete class nhưng use case không nên biết.
- Đừng nuốt exception. Map lỗi kỹ thuật thành error có ý nghĩa và giữ nguyên cause để điều tra.
- Nếu **method call trực tiếp khi không cần queue/history** vẫn rõ ràng hơn và thay đổi chưa có thật, hãy ghi kết luận “chưa cần pattern” trong ADR.

## Test bắt buộc

- Replay cùng operation không tạo kết quả thứ hai và vẫn giữ **mỗi command có handler duy nhất và idempotency key**.
- Concurrency test tại boundary nơi **duplicate delivery hoặc handler version mismatch** có thể xảy ra.
- Migration test so sánh old/new implementation trên cùng fixture hoặc shadow traffic.
- Telemetry test/assertion cho correlation ID, error class và decision version.

## Deliverable

```text
solution/
├── before.php
├── after.php
├── tests/
│   ├── CharacterizationTest.php
│   ├── ContractOrBehaviorTest.php
│   └── FailurePathTest.php
└── ADR.md
```

Bài Production cần thêm migration.md, dashboard.md và runbook.md.

## Tiêu chí tự chấm

- [ ] Tên class/method phản ánh đúng **Command bus đa worker**.
- [ ] Invariant **mỗi command có handler duy nhất và idempotency key** có test tự động.
- [ ] Failure **duplicate delivery hoặc handler version mismatch** có outcome xác định.
- [ ] Client biết ít concrete detail hơn sau refactor.
- [ ] Diagram, code và test mô tả cùng một boundary.
- [ ] Có giải thích khi nào **method call trực tiếp khi không cần queue/history** tốt hơn.
- [ ] Có migration, rollback, metric và runbook.

## Câu hỏi design review

1. Trục thay đổi thật sự của **Command bus đa worker** là gì, và `CommandBus` cô lập nó ở đâu?
2. Invariant **mỗi command có handler duy nhất và idempotency key** được enforce ở layer nào? Có đường đi nào bypass không?
3. Khi **duplicate delivery hoặc handler version mismatch** xảy ra, client nhận error gì và operator nhìn thấy evidence nào?
4. Pattern này làm dependency graph tốt hơn ở điểm nào, và tạo thêm chi phí gì?
5. Dấu hiệu nào cho biết nên quay lại phương án **method call trực tiếp khi không cần queue/history**?

## Lời giải tham khảo

Với **Command bus đa worker**, hãy hoàn thành test và tự vẽ dependency trước/sau rồi mới mở [`SOLUTION.md`](SOLUTION.md). Khi đối chiếu, ưu tiên invariant, failure semantics và khả năng mở rộng của Command thay vì đếm class.
