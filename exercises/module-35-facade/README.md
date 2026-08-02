# Module 35 — Production: Facade

## Vì sao bài này tồn tại?

**Onboarding khách hàng** là tình huống độc lập được xây dựng riêng cho Facade. Bài không lấy từ một source ẩn: bạn tự tạo phiên bản `before` tối thiểu dựa trên mô tả dưới đây, sau đó dùng test để chứng minh refactor không đổi behavior. Bài Production giả định hệ thống đã chạy thật. Ngoài cấu trúc code, lời giải phải xử lý migration, failure, idempotency hoặc observability phù hợp với use case.

## Câu chuyện nghiệp vụ

Hệ thống cần xử lý **Onboarding khách hàng**. `CustomerOnboardingFacade` đang để API gọi trực tiếp identity, provisioning và notification, không có trạng thái resume/compensation.

Invariant trung tâm của bài **Facade** là:

> **workflow có trạng thái, compensation và audit.**

Ở cấp Production, **Facade** phải bảo vệ invariant dưới retry/concurrency hoặc partial failure, đồng thời có migration seam, telemetry, rollback trigger và cleanup condition sau rollout.

Failure bắt buộc phải được mô hình hóa:

> **partial success giữa KYC, account và notification.**

## Trạng thái code ban đầu

```php
final class CustomerOnboardingFacade
{
    public function handle(array $input): mixed
    {
        // validation chung, lựa chọn biến thể và side effect
        // đang bị trộn trong một method.
        throw new RuntimeException('Hãy dựng phiên bản before phù hợp đề bài.');
    }
}
```

Đây là skeleton định hướng, không phải lời giải. Hãy thêm dữ liệu và behavior nhỏ nhất đủ tái hiện pain point của **Onboarding khách hàng**.

## Mô hình thiết kế cần hướng tới

```mermaid
sequenceDiagram
    participant C as OnboardingController
    participant F as CustomerOnboardingFacade
    participant I as IdentityVerification
    participant A as AccountProvisioning
    participant N as WelcomeNotification
    C->>F: onboard(command)
    F->>I: verify identity
    F->>A: provision account
    F->>N: schedule welcome message
    F-->>C: onboarding result + status
```

Facade điều phối use case nhiều subsystem và trả trạng thái có thể resume. Các bước có side effect cần idempotency key, compensation/manual recovery và audit trail.

## Nhiệm vụ

1. Khóa behavior hiện tại của **Onboarding khách hàng** bằng characterization test và log một trace hoàn chỉnh.
2. Xác định source of truth, transaction boundary và side effect bên ngoài quanh `OnboardingFacade`.
3. Tạo một migration seam để chạy song song implementation cũ/mới; so sánh kết quả trước khi chuyển traffic.
4. Mô phỏng failure **partial success giữa KYC, account và notification** và chứng minh retry/replay không phá invariant.
5. Bổ sung saga state, recovery command và observability; định nghĩa metric, alert và rollback trigger.
6. Viết ADR ghi rõ evidence, phương án baseline, cleanup condition và người sở hữu runbook.

## Dữ liệu thử tối thiểu

- Hai scenario hợp lệ đại diện cho hai biến thể khác nhau.
- Một boundary value liên quan trực tiếp tới **workflow có trạng thái, compensation và audit**.
- Một scenario tạo ra **partial success giữa KYC, account và notification**.
- Một operation lặp lại và một scenario concurrent/replay.

## Gợi ý triển khai

- Bắt đầu bằng behavior và invariant; chỉ tạo abstraction sau khi xác định đúng trục thay đổi.
- Đặt tên method theo domain của **Onboarding khách hàng**, tránh `execute(mixed $data)` hoặc `handleAnything()`.
- Tách selection/wiring khỏi business calculation; composition root có thể biết concrete class nhưng use case không nên biết.
- Đừng nuốt exception. Map lỗi kỹ thuật thành error có ý nghĩa và giữ nguyên cause để điều tra.
- Nếu **gọi trực tiếp khi workflow chỉ có một bước** vẫn rõ ràng hơn và thay đổi chưa có thật, hãy ghi kết luận “chưa cần pattern” trong ADR.

## Test bắt buộc

- Replay cùng operation không tạo kết quả thứ hai và vẫn giữ **workflow có trạng thái, compensation và audit**.
- Concurrency test tại boundary nơi **partial success giữa KYC, account và notification** có thể xảy ra.
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

- [ ] Tên class/method phản ánh đúng **Onboarding khách hàng**.
- [ ] Invariant **workflow có trạng thái, compensation và audit** có test tự động.
- [ ] Failure **partial success giữa KYC, account và notification** có outcome xác định.
- [ ] Client biết ít concrete detail hơn sau refactor.
- [ ] Diagram, code và test mô tả cùng một boundary.
- [ ] Có giải thích khi nào **gọi trực tiếp khi workflow chỉ có một bước** tốt hơn.
- [ ] Có migration, rollback, metric và runbook.

## Câu hỏi design review

1. Trục thay đổi thật sự của **Onboarding khách hàng** là gì, và `OnboardingFacade` cô lập nó ở đâu?
2. Invariant **workflow có trạng thái, compensation và audit** được enforce ở layer nào? Có đường đi nào bypass không?
3. Khi **partial success giữa KYC, account và notification** xảy ra, client nhận error gì và operator nhìn thấy evidence nào?
4. Pattern này làm dependency graph tốt hơn ở điểm nào, và tạo thêm chi phí gì?
5. Dấu hiệu nào cho biết nên quay lại phương án **gọi trực tiếp khi workflow chỉ có một bước**?

## Lời giải tham khảo

Với **Onboarding khách hàng**, hãy hoàn thành test và tự vẽ dependency trước/sau rồi mới mở [`SOLUTION.md`](SOLUTION.md). Khi đối chiếu, ưu tiên invariant, failure semantics và khả năng mở rộng của Facade thay vì đếm class.
