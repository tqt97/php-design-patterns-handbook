# Module 42 — Production: Repository

## Vì sao bài này tồn tại?

**Order aggregate persistence** là tình huống độc lập được xây dựng riêng cho Repository. Bài không lấy từ một source ẩn: bạn tự tạo phiên bản `before` tối thiểu dựa trên mô tả dưới đây, sau đó dùng test để chứng minh refactor không đổi behavior. Bài Production giả định hệ thống đã chạy thật. Ngoài cấu trúc code, lời giải phải xử lý migration, failure, idempotency hoặc observability phù hợp với use case.

## Câu chuyện nghiệp vụ

Hệ thống cần xử lý **Order aggregate persistence**. `OrderApplicationService` đang expose ORM model/partial entity và không kiểm tra expected version khi save aggregate.

Invariant trung tâm của bài **Repository** là:

> **aggregate save atomic theo version.**

Ở cấp Production, **Repository** phải bảo vệ invariant dưới retry/concurrency hoặc partial failure, đồng thời có migration seam, telemetry, rollback trigger và cleanup condition sau rollout.

Failure bắt buộc phải được mô hình hóa:

> **lost update hoặc eager graph quá lớn.**

## Trạng thái code ban đầu

```php
final class OrderApplicationService
{
    public function handle(array $input): mixed
    {
        // validation chung, lựa chọn biến thể và side effect
        // đang bị trộn trong một method.
        throw new RuntimeException('Hãy dựng phiên bản before phù hợp đề bài.');
    }
}
```

Đây là skeleton định hướng, không phải lời giải. Hãy thêm dữ liệu và behavior nhỏ nhất đủ tái hiện pain point của **Order aggregate persistence**.

## Mô hình thiết kế cần hướng tới

```mermaid
sequenceDiagram
    participant A as OrderApplicationService
    participant R as OrderRepository
    participant S as Persistence Store
    A->>R: ofId(orderId)
    R->>S: load aggregate data/version
    S-->>R: data + version
    R-->>A: Order aggregate
    A->>A: execute domain behavior
    A->>R: save(order, expectedVersion)
    R->>S: conditional write
```

Repository bảo toàn aggregate boundary và optimistic version. Query/reporting đi qua read model khác; không expose ORM builder hoặc partial entity làm phá invariant.

## Nhiệm vụ

1. Khóa behavior hiện tại của **Order aggregate persistence** bằng characterization test và log một trace hoàn chỉnh.
2. Xác định source of truth, transaction boundary và side effect bên ngoài quanh `OrderRepository`.
3. Tạo một migration seam để chạy song song implementation cũ/mới; so sánh kết quả trước khi chuyển traffic.
4. Mô phỏng failure **lost update hoặc eager graph quá lớn** và chứng minh retry/replay không phá invariant.
5. Bổ sung optimistic concurrency, unit of work và repository contract test; định nghĩa metric, alert và rollback trigger.
6. Viết ADR ghi rõ evidence, phương án baseline, cleanup condition và người sở hữu runbook.

## Dữ liệu thử tối thiểu

- Hai scenario hợp lệ đại diện cho hai biến thể khác nhau.
- Một boundary value liên quan trực tiếp tới **aggregate save atomic theo version**.
- Một scenario tạo ra **lost update hoặc eager graph quá lớn**.
- Một operation lặp lại và một scenario concurrent/replay.

## Gợi ý triển khai

- Bắt đầu bằng behavior và invariant; chỉ tạo abstraction sau khi xác định đúng trục thay đổi.
- Đặt tên method theo domain của **Order aggregate persistence**, tránh `execute(mixed $data)` hoặc `handleAnything()`.
- Tách selection/wiring khỏi business calculation; composition root có thể biết concrete class nhưng use case không nên biết.
- Đừng nuốt exception. Map lỗi kỹ thuật thành error có ý nghĩa và giữ nguyên cause để điều tra.
- Nếu **Eloquent trực tiếp cho CRUD đơn giản** vẫn rõ ràng hơn và thay đổi chưa có thật, hãy ghi kết luận “chưa cần pattern” trong ADR.

## Test bắt buộc

- Replay cùng operation không tạo kết quả thứ hai và vẫn giữ **aggregate save atomic theo version**.
- Concurrency test tại boundary nơi **lost update hoặc eager graph quá lớn** có thể xảy ra.
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

- [ ] Tên class/method phản ánh đúng **Order aggregate persistence**.
- [ ] Invariant **aggregate save atomic theo version** có test tự động.
- [ ] Failure **lost update hoặc eager graph quá lớn** có outcome xác định.
- [ ] Client biết ít concrete detail hơn sau refactor.
- [ ] Diagram, code và test mô tả cùng một boundary.
- [ ] Có giải thích khi nào **Eloquent trực tiếp cho CRUD đơn giản** tốt hơn.
- [ ] Có migration, rollback, metric và runbook.

## Câu hỏi design review

1. Trục thay đổi thật sự của **Order aggregate persistence** là gì, và `OrderRepository` cô lập nó ở đâu?
2. Invariant **aggregate save atomic theo version** được enforce ở layer nào? Có đường đi nào bypass không?
3. Khi **lost update hoặc eager graph quá lớn** xảy ra, client nhận error gì và operator nhìn thấy evidence nào?
4. Pattern này làm dependency graph tốt hơn ở điểm nào, và tạo thêm chi phí gì?
5. Dấu hiệu nào cho biết nên quay lại phương án **Eloquent trực tiếp cho CRUD đơn giản**?

## Lời giải tham khảo

Với **Order aggregate persistence**, hãy hoàn thành test và tự vẽ dependency trước/sau rồi mới mở [`SOLUTION.md`](SOLUTION.md). Khi đối chiếu, ưu tiên invariant, failure semantics và khả năng mở rộng của Repository thay vì đếm class.
