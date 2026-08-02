# Lời giải tham khảo — Specification (Production)

## Kết luận thiết kế

Lời giải chọn `EligibilitySpecification` làm boundary vì nó bao quanh phần thay đổi của **Compliance policy engine** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **policy version và explanation đi cùng decision**, không phải chứng minh rằng mọi bài toán đều cần Specification.

## Sơ đồ lời giải

```mermaid
sequenceDiagram
    participant C as Client
    participant A as Compliance policy engine
    participant B as EligibilitySpecification
    participant S as Source of truth
    participant O as External side effect
    C->>A: request + operation/version
    A->>B: execute domain intent
    B->>S: read/write with guard
    B->>O: idempotent side effect
    alt rule version drift hoặc partial data
        O-->>B: ambiguous/transient result
        B-->>A: classified failure + evidence
    else success
        B-->>A: result preserving invariant
    end
    A-->>C: stable application response
```

## Các bước refactor

1. Định nghĩa `EligibilitySpecification` bằng ngôn ngữ của **Compliance policy engine**, không dùng interface chung chung.
2. Bọc implementation cũ sau cùng contract để tạo seam mà chưa thay behavior.
3. Thêm implementation mới và chạy dual-read/shadow compare; lưu mismatch có dữ liệu điều tra.
4. Đưa guard cho invariant **policy version và explanation đi cùng decision** gần source of truth nhất.
5. Classify **rule version drift hoặc partial data** thành domain/transient/permanent và gắn retry hoặc compensation tương ứng.
6. Triển khai versioned policy, fact snapshot và decision audit; chỉ chuyển traffic khi metric đạt ngưỡng và rollback đã được diễn tập.

## Phác thảo contract

```php
interface EligibilitySpecification
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Compliance policy engine**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Specification phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `CompliancePolicyEngineBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **policy version và explanation đi cùng decision.** trên output/state, không assert tên concrete class.
- `CompliancePolicyEngineFailureTest`: tạo **rule version drift hoặc partial data.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `SpecificationContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `CompliancePolicyEngineReplayAndConcurrencyTest`: gửi lại operation hoặc tạo race tại source of truth; assert idempotency/version guard thay vì chỉ mock call count.
- `CompliancePolicyEngineMigrationTest`: chạy old/new trên cùng fixture hoặc shadow input, lưu mismatch có correlation ID và kiểm tra rollback trigger.
- Telemetry assertion: log/metric phải chứa operation, decision/version và failure class đủ để điều tra **Compliance policy engine**.
## Failure walkthrough

Khi **rule version drift hoặc partial data**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **policy version và explanation đi cùng decision**. Nếu side effect có thể đã thành công, lần retry phải dựa vào operation record/idempotency evidence thay vì đoán.

## Trade-off và phương án thay thế

Trong **Compliance policy engine**, Specification chỉ đáng giữ khi nó giảm rủi ro của **rule version drift hoặc partial data.** hoặc cho phép migration/rollback có evidence. Chi phí thật gồm wiring, version compatibility, telemetry, runbook và ownership khi on-call — không chỉ số class.

Baseline cần so sánh là **predicate inline khi rule không tái sử dụng**. Nếu shadow comparison không cho thấy blast radius, correctness hoặc recovery tốt hơn, hãy giữ baseline và ghi lại trigger xem xét lại. Trước khi rollout cần xác định source of truth, cleanup condition cho implementation cũ và metric chứng minh invariant **policy version và explanation đi cùng decision.** tiếp tục được giữ.
## Dấu hiệu lời giải chưa đạt

- Boundary của Specification không dùng ngôn ngữ **Compliance policy engine**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **policy version và explanation đi cùng decision** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Migration không có shadow evidence/rollback, hoặc retry vẫn có thể tạo side effect kép.

## Câu hỏi mở rộng

- Với **Compliance policy engine**, metric nào chứng minh Specification giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Specification

Trong **Lời giải tham khảo — Specification (Production)** cấp Production, Specification đóng gói predicate có thể kết hợp; kết quả cần reason/explanation và semantics rõ khi thiếu dữ kiện hoặc rule không áp dụng.

### Test focus

Ở cấp **Production**, truth-table test cho AND/OR/NOT, versioned facts và reason output. Thêm failure/concurrency hoặc retry case, telemetry assertion và recovery verification.

### Bằng chứng nên lưu

Với **Compliance policy engine**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Specification. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
