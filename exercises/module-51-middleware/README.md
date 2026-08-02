# Module 51 — Production: Middleware

## Vì sao bài này tồn tại?

**Multi-tenant HTTP stack** là tình huống độc lập được xây dựng riêng cho Middleware. Bài không lấy từ một source ẩn: bạn tự tạo phiên bản `before` tối thiểu dựa trên mô tả dưới đây, sau đó dùng test để chứng minh refactor không đổi behavior. Bài Production giả định hệ thống đã chạy thật. Ngoài cấu trúc code, lời giải phải xử lý migration, failure, idempotency hoặc observability phù hợp với use case.

## Câu chuyện nghiệp vụ

Hệ thống cần xử lý **Multi-tenant HTTP stack**. `TenantHttpKernel` đang lưu tenant context toàn cục trong worker dài sống và không đảm bảo middleware ordering.

Invariant trung tâm của bài **Middleware** là:

> **tenant context request-scoped và cleanup chắc chắn.**

Ở cấp Production, **Middleware** phải bảo vệ invariant dưới retry/concurrency hoặc partial failure, đồng thời có migration seam, telemetry, rollback trigger và cleanup condition sau rollout.

Failure bắt buộc phải được mô hình hóa:

> **context leak, wrong ordering hoặc exception bypass cleanup.**

## Trạng thái code ban đầu

```php
final class TenantHttpKernel
{
    public function handle(array $input): mixed
    {
        // validation chung, lựa chọn biến thể và side effect
        // đang bị trộn trong một method.
        throw new RuntimeException('Hãy dựng phiên bản before phù hợp đề bài.');
    }
}
```

Đây là skeleton định hướng, không phải lời giải. Hãy thêm dữ liệu và behavior nhỏ nhất đủ tái hiện pain point của **Multi-tenant HTTP stack**.

## Mô hình thiết kế cần hướng tới

```mermaid
flowchart LR
    R[HTTP Request] --> C[Correlation]
    C --> T[Tenant Resolution]
    T --> A[Authentication]
    A --> Z[Authorization]
    Z --> L[Tenant Rate Limit]
    L --> H[Application Handler]
    H --> X[Context Cleanup]
```

Tenant context phải scoped theo request và được cleanup trong worker dài sống. Ordering bảo đảm không query tenant data trước khi resolve/authorize tenant.

## Nhiệm vụ

1. Khóa behavior hiện tại của **Multi-tenant HTTP stack** bằng characterization test và log một trace hoàn chỉnh.
2. Xác định source of truth, transaction boundary và side effect bên ngoài quanh `TenantKernel`.
3. Tạo một migration seam để chạy song song implementation cũ/mới; so sánh kết quả trước khi chuyển traffic.
4. Mô phỏng failure **context leak, wrong ordering hoặc exception bypass cleanup** và chứng minh retry/replay không phá invariant.
5. Bổ sung scoped container, finally cleanup và integration test; định nghĩa metric, alert và rollback trigger.
6. Viết ADR ghi rõ evidence, phương án baseline, cleanup condition và người sở hữu runbook.

## Dữ liệu thử tối thiểu

- Hai scenario hợp lệ đại diện cho hai biến thể khác nhau.
- Một boundary value liên quan trực tiếp tới **tenant context request-scoped và cleanup chắc chắn**.
- Một scenario tạo ra **context leak, wrong ordering hoặc exception bypass cleanup**.
- Một operation lặp lại và một scenario concurrent/replay.

## Gợi ý triển khai

- Bắt đầu bằng behavior và invariant; chỉ tạo abstraction sau khi xác định đúng trục thay đổi.
- Đặt tên method theo domain của **Multi-tenant HTTP stack**, tránh `execute(mixed $data)` hoặc `handleAnything()`.
- Tách selection/wiring khỏi business calculation; composition root có thể biết concrete class nhưng use case không nên biết.
- Đừng nuốt exception. Map lỗi kỹ thuật thành error có ý nghĩa và giữ nguyên cause để điều tra.
- Nếu **controller check trực tiếp cho endpoint nhỏ** vẫn rõ ràng hơn và thay đổi chưa có thật, hãy ghi kết luận “chưa cần pattern” trong ADR.

## Test bắt buộc

- Replay cùng operation không tạo kết quả thứ hai và vẫn giữ **tenant context request-scoped và cleanup chắc chắn**.
- Concurrency test tại boundary nơi **context leak, wrong ordering hoặc exception bypass cleanup** có thể xảy ra.
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

- [ ] Tên class/method phản ánh đúng **Multi-tenant HTTP stack**.
- [ ] Invariant **tenant context request-scoped và cleanup chắc chắn** có test tự động.
- [ ] Failure **context leak, wrong ordering hoặc exception bypass cleanup** có outcome xác định.
- [ ] Client biết ít concrete detail hơn sau refactor.
- [ ] Diagram, code và test mô tả cùng một boundary.
- [ ] Có giải thích khi nào **controller check trực tiếp cho endpoint nhỏ** tốt hơn.
- [ ] Có migration, rollback, metric và runbook.

## Câu hỏi design review

1. Trục thay đổi thật sự của **Multi-tenant HTTP stack** là gì, và `TenantKernel` cô lập nó ở đâu?
2. Invariant **tenant context request-scoped và cleanup chắc chắn** được enforce ở layer nào? Có đường đi nào bypass không?
3. Khi **context leak, wrong ordering hoặc exception bypass cleanup** xảy ra, client nhận error gì và operator nhìn thấy evidence nào?
4. Pattern này làm dependency graph tốt hơn ở điểm nào, và tạo thêm chi phí gì?
5. Dấu hiệu nào cho biết nên quay lại phương án **controller check trực tiếp cho endpoint nhỏ**?

## Lời giải tham khảo

Với **Multi-tenant HTTP stack**, hãy hoàn thành test và tự vẽ dependency trước/sau rồi mới mở [`SOLUTION.md`](SOLUTION.md). Khi đối chiếu, ưu tiên invariant, failure semantics và khả năng mở rộng của Middleware thay vì đếm class.
