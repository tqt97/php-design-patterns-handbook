# Module 39 — Production: Observer

## Vì sao bài này tồn tại?

**Event-driven projection** là tình huống độc lập được xây dựng riêng cho Observer. Bài không lấy từ một source ẩn: bạn tự tạo phiên bản `before` tối thiểu dựa trên mô tả dưới đây, sau đó dùng test để chứng minh refactor không đổi behavior. Bài Production giả định hệ thống đã chạy thật. Ngoài cấu trúc code, lời giải phải xử lý migration, failure, idempotency hoặc observability phù hợp với use case.

## Câu chuyện nghiệp vụ

Hệ thống cần xử lý **Event-driven projection**. `ProjectionUpdater` đang cập nhật nhiều read model từ một consumer chung, thiếu checkpoint và rebuild strategy.

Invariant trung tâm của bài **Observer** là:

> **projection eventually consistent và replayable.**

Ở cấp Production, **Observer** phải bảo vệ invariant dưới retry/concurrency hoặc partial failure, đồng thời có migration seam, telemetry, rollback trigger và cleanup condition sau rollout.

Failure bắt buộc phải được mô hình hóa:

> **duplicate/out-of-order event.**

## Trạng thái code ban đầu

```php
final class ProjectionUpdater
{
    public function handle(array $input): mixed
    {
        // validation chung, lựa chọn biến thể và side effect
        // đang bị trộn trong một method.
        throw new RuntimeException('Hãy dựng phiên bản before phù hợp đề bài.');
    }
}
```

Đây là skeleton định hướng, không phải lời giải. Hãy thêm dữ liệu và behavior nhỏ nhất đủ tái hiện pain point của **Event-driven projection**.

## Mô hình thiết kế cần hướng tới

```mermaid
sequenceDiagram
    participant E as Event Store/Outbox
    participant D as Dispatcher
    participant O as OrderProjection
    participant R as RevenueProjection
    E->>D: event(offset, version)
    par independent consumers
      D->>O: apply event
      D->>R: apply event
    end
    O->>O: persist checkpoint
    R->>R: persist checkpoint
```

Mỗi projection có checkpoint và idempotent apply. Rebuild, schema evolution, out-of-order event và poison message phải có chiến lược riêng.

## Nhiệm vụ

1. Khóa behavior hiện tại của **Event-driven projection** bằng characterization test và log một trace hoàn chỉnh.
2. Xác định source of truth, transaction boundary và side effect bên ngoài quanh `EventDispatcher`.
3. Tạo một migration seam để chạy song song implementation cũ/mới; so sánh kết quả trước khi chuyển traffic.
4. Mô phỏng failure **duplicate/out-of-order event** và chứng minh retry/replay không phá invariant.
5. Bổ sung outbox, inbox, checkpoint và rebuild runbook; định nghĩa metric, alert và rollback trigger.
6. Viết ADR ghi rõ evidence, phương án baseline, cleanup condition và người sở hữu runbook.

## Dữ liệu thử tối thiểu

- Hai scenario hợp lệ đại diện cho hai biến thể khác nhau.
- Một boundary value liên quan trực tiếp tới **projection eventually consistent và replayable**.
- Một scenario tạo ra **duplicate/out-of-order event**.
- Một operation lặp lại và một scenario concurrent/replay.

## Gợi ý triển khai

- Bắt đầu bằng behavior và invariant; chỉ tạo abstraction sau khi xác định đúng trục thay đổi.
- Đặt tên method theo domain của **Event-driven projection**, tránh `execute(mixed $data)` hoặc `handleAnything()`.
- Tách selection/wiring khỏi business calculation; composition root có thể biết concrete class nhưng use case không nên biết.
- Đừng nuốt exception. Map lỗi kỹ thuật thành error có ý nghĩa và giữ nguyên cause để điều tra.
- Nếu **gọi trực tiếp khi side effect bắt buộc đồng bộ** vẫn rõ ràng hơn và thay đổi chưa có thật, hãy ghi kết luận “chưa cần pattern” trong ADR.

## Test bắt buộc

- Replay cùng operation không tạo kết quả thứ hai và vẫn giữ **projection eventually consistent và replayable**.
- Concurrency test tại boundary nơi **duplicate/out-of-order event** có thể xảy ra.
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

- [ ] Tên class/method phản ánh đúng **Event-driven projection**.
- [ ] Invariant **projection eventually consistent và replayable** có test tự động.
- [ ] Failure **duplicate/out-of-order event** có outcome xác định.
- [ ] Client biết ít concrete detail hơn sau refactor.
- [ ] Diagram, code và test mô tả cùng một boundary.
- [ ] Có giải thích khi nào **gọi trực tiếp khi side effect bắt buộc đồng bộ** tốt hơn.
- [ ] Có migration, rollback, metric và runbook.

## Câu hỏi design review

1. Trục thay đổi thật sự của **Event-driven projection** là gì, và `EventDispatcher` cô lập nó ở đâu?
2. Invariant **projection eventually consistent và replayable** được enforce ở layer nào? Có đường đi nào bypass không?
3. Khi **duplicate/out-of-order event** xảy ra, client nhận error gì và operator nhìn thấy evidence nào?
4. Pattern này làm dependency graph tốt hơn ở điểm nào, và tạo thêm chi phí gì?
5. Dấu hiệu nào cho biết nên quay lại phương án **gọi trực tiếp khi side effect bắt buộc đồng bộ**?

## Lời giải tham khảo

Với **Event-driven projection**, hãy hoàn thành test và tự vẽ dependency trước/sau rồi mới mở [`SOLUTION.md`](SOLUTION.md). Khi đối chiếu, ưu tiên invariant, failure semantics và khả năng mở rộng của Observer thay vì đếm class.
