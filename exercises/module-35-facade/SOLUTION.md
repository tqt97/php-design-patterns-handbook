# Lời giải tham khảo — Facade (Production)

## Kết luận thiết kế

Lời giải chọn `OnboardingFacade` làm boundary vì nó bao quanh phần thay đổi của **Onboarding khách hàng** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **workflow có trạng thái, compensation và audit**, không phải chứng minh rằng mọi bài toán đều cần Facade.

## Sơ đồ lời giải

```mermaid
sequenceDiagram
    participant C as Client
    participant A as Onboarding khách hàng
    participant B as OnboardingFacade
    participant S as Source of truth
    participant O as External side effect
    C->>A: request + operation/version
    A->>B: execute domain intent
    B->>S: read/write with guard
    B->>O: idempotent side effect
    alt partial success giữa KYC, account và notification
        O-->>B: ambiguous/transient result
        B-->>A: classified failure + evidence
    else success
        B-->>A: result preserving invariant
    end
    A-->>C: stable application response
```

## Các bước refactor

1. Định nghĩa `OnboardingFacade` bằng ngôn ngữ của **Onboarding khách hàng**, không dùng interface chung chung.
2. Bọc implementation cũ sau cùng contract để tạo seam mà chưa thay behavior.
3. Thêm implementation mới và chạy dual-read/shadow compare; lưu mismatch có dữ liệu điều tra.
4. Đưa guard cho invariant **workflow có trạng thái, compensation và audit** gần source of truth nhất.
5. Classify **partial success giữa KYC, account và notification** thành domain/transient/permanent và gắn retry hoặc compensation tương ứng.
6. Triển khai saga state, recovery command và observability; chỉ chuyển traffic khi metric đạt ngưỡng và rollback đã được diễn tập.

## Phác thảo contract

```php
interface OnboardingFacade
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Onboarding khách hàng**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Facade phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `OnboardingKhachHangBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **workflow có trạng thái, compensation và audit.** trên output/state, không assert tên concrete class.
- `OnboardingKhachHangFailureTest`: tạo **partial success giữa KYC, account và notification.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `FacadeContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `OnboardingKhachHangReplayAndConcurrencyTest`: gửi lại operation hoặc tạo race tại source of truth; assert idempotency/version guard thay vì chỉ mock call count.
- `OnboardingKhachHangMigrationTest`: chạy old/new trên cùng fixture hoặc shadow input, lưu mismatch có correlation ID và kiểm tra rollback trigger.
- Telemetry assertion: log/metric phải chứa operation, decision/version và failure class đủ để điều tra **Onboarding khách hàng**.
## Failure walkthrough

Khi **partial success giữa KYC, account và notification**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **workflow có trạng thái, compensation và audit**. Nếu side effect có thể đã thành công, lần retry phải dựa vào operation record/idempotency evidence thay vì đoán.

## Trade-off và phương án thay thế

Trong **Onboarding khách hàng**, Facade chỉ đáng giữ khi nó giảm rủi ro của **partial success giữa KYC, account và notification.** hoặc cho phép migration/rollback có evidence. Chi phí thật gồm wiring, version compatibility, telemetry, runbook và ownership khi on-call — không chỉ số class.

Baseline cần so sánh là **gọi trực tiếp khi workflow chỉ có một bước**. Nếu shadow comparison không cho thấy blast radius, correctness hoặc recovery tốt hơn, hãy giữ baseline và ghi lại trigger xem xét lại. Trước khi rollout cần xác định source of truth, cleanup condition cho implementation cũ và metric chứng minh invariant **workflow có trạng thái, compensation và audit.** tiếp tục được giữ.
## Dấu hiệu lời giải chưa đạt

- Boundary của Facade không dùng ngôn ngữ **Onboarding khách hàng**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **workflow có trạng thái, compensation và audit** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Migration không có shadow evidence/rollback, hoặc retry vẫn có thể tạo side effect kép.

## Câu hỏi mở rộng

- Với **Onboarding khách hàng**, metric nào chứng minh Facade giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Facade

Ở **Lời giải tham khảo — Facade (Production)** cấp Production, Facade cung cấp entry point có mục tiêu rõ cho subsystem; nó không được che giấu state machine, transaction boundary hoặc failure semantics khiến caller không thể recovery.

### Test focus

Ở cấp **Production**, test orchestration order, partial failure, compensation và idempotent resume. Thêm failure/concurrency hoặc retry case, telemetry assertion và recovery verification.

### Bằng chứng nên lưu

Với **Onboarding khách hàng**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Facade. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
