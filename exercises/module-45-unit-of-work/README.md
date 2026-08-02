# Module 45 — Production: Unit of Work

## Vì sao bài này tồn tại?

**Cross-aggregate transaction** là tình huống độc lập được xây dựng riêng cho Unit of Work. Bài không lấy từ một source ẩn: bạn tự tạo phiên bản `before` tối thiểu dựa trên mô tả dưới đây, sau đó dùng test để chứng minh refactor không đổi behavior. Bài Production giả định hệ thống đã chạy thật. Ngoài cấu trúc code, lời giải phải xử lý migration, failure, idempotency hoặc observability phù hợp với use case.

## Câu chuyện nghiệp vụ

Hệ thống cần xử lý **Cross-aggregate transaction**. `CrossAggregateApplicationService` đang cố giữ database write và network call trong cùng transaction tưởng tượng, làm lock dài và recovery mơ hồ.

Invariant trung tâm của bài **Unit of Work** là:

> **transaction boundary không bao network call.**

Ở cấp Production, **Unit of Work** phải bảo vệ invariant dưới retry/concurrency hoặc partial failure, đồng thời có migration seam, telemetry, rollback trigger và cleanup condition sau rollout.

Failure bắt buộc phải được mô hình hóa:

> **deadlock, retry hoặc nested transaction sai.**

## Trạng thái code ban đầu

```php
final class CrossAggregateApplicationService
{
    public function handle(array $input): mixed
    {
        // validation chung, lựa chọn biến thể và side effect
        // đang bị trộn trong một method.
        throw new RuntimeException('Hãy dựng phiên bản before phù hợp đề bài.');
    }
}
```

Đây là skeleton định hướng, không phải lời giải. Hãy thêm dữ liệu và behavior nhỏ nhất đủ tái hiện pain point của **Cross-aggregate transaction**.

## Mô hình thiết kế cần hướng tới

```mermaid
sequenceDiagram
    participant A as Application Service
    participant U as UnitOfWork
    participant O as OrderRepository
    participant I as InventoryRepository
    participant X as Outbox
    A->>U: transactional(callback)
    A->>O: save order
    A->>I: save reservation
    A->>X: append integration event
    U->>U: commit atomically
```

Chỉ gom các write cùng database/consistency boundary. Remote calls không nằm trong transaction; outbox được ghi cùng commit để phát event sau đó.

## Nhiệm vụ

1. Khóa behavior hiện tại của **Cross-aggregate transaction** bằng characterization test và log một trace hoàn chỉnh.
2. Xác định source of truth, transaction boundary và side effect bên ngoài quanh `ApplicationService`.
3. Tạo một migration seam để chạy song song implementation cũ/mới; so sánh kết quả trước khi chuyển traffic.
4. Mô phỏng failure **deadlock, retry hoặc nested transaction sai** và chứng minh retry/replay không phá invariant.
5. Bổ sung retry-safe unit, after-commit outbox và lock order; định nghĩa metric, alert và rollback trigger.
6. Viết ADR ghi rõ evidence, phương án baseline, cleanup condition và người sở hữu runbook.

## Dữ liệu thử tối thiểu

- Hai scenario hợp lệ đại diện cho hai biến thể khác nhau.
- Một boundary value liên quan trực tiếp tới **transaction boundary không bao network call**.
- Một scenario tạo ra **deadlock, retry hoặc nested transaction sai**.
- Một operation lặp lại và một scenario concurrent/replay.

## Gợi ý triển khai

- Bắt đầu bằng behavior và invariant; chỉ tạo abstraction sau khi xác định đúng trục thay đổi.
- Đặt tên method theo domain của **Cross-aggregate transaction**, tránh `execute(mixed $data)` hoặc `handleAnything()`.
- Tách selection/wiring khỏi business calculation; composition root có thể biết concrete class nhưng use case không nên biết.
- Đừng nuốt exception. Map lỗi kỹ thuật thành error có ý nghĩa và giữ nguyên cause để điều tra.
- Nếu **transaction script trực tiếp khi scope nhỏ** vẫn rõ ràng hơn và thay đổi chưa có thật, hãy ghi kết luận “chưa cần pattern” trong ADR.

## Test bắt buộc

- Replay cùng operation không tạo kết quả thứ hai và vẫn giữ **transaction boundary không bao network call**.
- Concurrency test tại boundary nơi **deadlock, retry hoặc nested transaction sai** có thể xảy ra.
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

- [ ] Tên class/method phản ánh đúng **Cross-aggregate transaction**.
- [ ] Invariant **transaction boundary không bao network call** có test tự động.
- [ ] Failure **deadlock, retry hoặc nested transaction sai** có outcome xác định.
- [ ] Client biết ít concrete detail hơn sau refactor.
- [ ] Diagram, code và test mô tả cùng một boundary.
- [ ] Có giải thích khi nào **transaction script trực tiếp khi scope nhỏ** tốt hơn.
- [ ] Có migration, rollback, metric và runbook.

## Câu hỏi design review

1. Trục thay đổi thật sự của **Cross-aggregate transaction** là gì, và `ApplicationService` cô lập nó ở đâu?
2. Invariant **transaction boundary không bao network call** được enforce ở layer nào? Có đường đi nào bypass không?
3. Khi **deadlock, retry hoặc nested transaction sai** xảy ra, client nhận error gì và operator nhìn thấy evidence nào?
4. Pattern này làm dependency graph tốt hơn ở điểm nào, và tạo thêm chi phí gì?
5. Dấu hiệu nào cho biết nên quay lại phương án **transaction script trực tiếp khi scope nhỏ**?

## Lời giải tham khảo

Với **Cross-aggregate transaction**, hãy hoàn thành test và tự vẽ dependency trước/sau rồi mới mở [`SOLUTION.md`](SOLUTION.md). Khi đối chiếu, ưu tiên invariant, failure semantics và khả năng mở rộng của Unit of Work thay vì đếm class.
