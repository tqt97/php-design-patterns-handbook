# Lời giải tham khảo — Strategy (Production)

## Kết luận thiết kế

Lời giải chọn `PricingPolicy` làm boundary vì nó bao quanh phần thay đổi của **Pricing rollout** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **cùng input và policy version cho cùng kết quả**, không phải chứng minh rằng mọi bài toán đều cần Strategy.

## Sơ đồ lời giải

```mermaid
sequenceDiagram
    participant C as Client
    participant A as Pricing rollout
    participant B as PricingPolicy
    participant S as Source of truth
    participant O as External side effect
    C->>A: request + operation/version
    A->>B: execute domain intent
    B->>S: read/write with guard
    B->>O: idempotent side effect
    alt version drift hoặc fallback sai
        O-->>B: ambiguous/transient result
        B-->>A: classified failure + evidence
    else success
        B-->>A: result preserving invariant
    end
    A-->>C: stable application response
```

## Các bước refactor

1. Định nghĩa `PricingPolicy` bằng ngôn ngữ của **Pricing rollout**, không dùng interface chung chung.
2. Bọc implementation cũ sau cùng contract để tạo seam mà chưa thay behavior.
3. Thêm implementation mới và chạy dual-read/shadow compare; lưu mismatch có dữ liệu điều tra.
4. Đưa guard cho invariant **cùng input và policy version cho cùng kết quả** gần source of truth nhất.
5. Classify **version drift hoặc fallback sai** thành domain/transient/permanent và gắn retry hoặc compensation tương ứng.
6. Triển khai shadow compare, canary và rollback; chỉ chuyển traffic khi metric đạt ngưỡng và rollback đã được diễn tập.

## Phác thảo contract

```php
interface PricingPolicy
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Pricing rollout**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Strategy phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `PricingRolloutBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **cùng input và policy version cho cùng kết quả.** trên output/state, không assert tên concrete class.
- `PricingRolloutFailureTest`: tạo **version drift hoặc fallback sai.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `StrategyContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `PricingRolloutReplayAndConcurrencyTest`: gửi lại operation hoặc tạo race tại source of truth; assert idempotency/version guard thay vì chỉ mock call count.
- `PricingRolloutMigrationTest`: chạy old/new trên cùng fixture hoặc shadow input, lưu mismatch có correlation ID và kiểm tra rollback trigger.
- Telemetry assertion: log/metric phải chứa operation, decision/version và failure class đủ để điều tra **Pricing rollout**.
## Failure walkthrough

Khi **version drift hoặc fallback sai**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **cùng input và policy version cho cùng kết quả**. Nếu side effect có thể đã thành công, lần retry phải dựa vào operation record/idempotency evidence thay vì đoán.

## Trade-off và phương án thay thế

Trong **Pricing rollout**, Strategy chỉ đáng giữ khi nó giảm rủi ro của **version drift hoặc fallback sai.** hoặc cho phép migration/rollback có evidence. Chi phí thật gồm wiring, version compatibility, telemetry, runbook và ownership khi on-call — không chỉ số class.

Baseline cần so sánh là **switch nhỏ với hai nhánh ổn định**. Nếu shadow comparison không cho thấy blast radius, correctness hoặc recovery tốt hơn, hãy giữ baseline và ghi lại trigger xem xét lại. Trước khi rollout cần xác định source of truth, cleanup condition cho implementation cũ và metric chứng minh invariant **cùng input và policy version cho cùng kết quả.** tiếp tục được giữ.
## Dấu hiệu lời giải chưa đạt

- Boundary của Strategy không dùng ngôn ngữ **Pricing rollout**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **cùng input và policy version cho cùng kết quả** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Migration không có shadow evidence/rollback, hoặc retry vẫn có thể tạo side effect kép.

## Câu hỏi mở rộng

- Với **Pricing rollout**, metric nào chứng minh Strategy giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Strategy

Ở **Lời giải tham khảo — Strategy (Production)** cấp Production, selection tách khỏi calculation; registry chỉ ánh xạ context/key sang policy. Production cần version/cohort/fallback và telemetry cho unknown policy.

### Test focus

Ở cấp **Production**, dùng truth table và property test cho postcondition chung giữa các policy. Thêm failure/concurrency hoặc retry case, telemetry assertion và recovery verification.

### Bằng chứng nên lưu

Với **Pricing rollout**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Strategy. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
