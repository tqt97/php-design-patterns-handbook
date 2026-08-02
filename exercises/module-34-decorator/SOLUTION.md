# Lời giải tham khảo — Decorator (Production)

## Kết luận thiết kế

Lời giải chọn `ApiClient` làm boundary vì nó bao quanh phần thay đổi của **HTTP client resilient** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **timeout/retry/logging không thay contract nghiệp vụ**, không phải chứng minh rằng mọi bài toán đều cần Decorator.

## Sơ đồ lời giải

```mermaid
sequenceDiagram
    participant C as Client
    participant A as HTTP client resilient
    participant B as ApiClient
    participant S as Source of truth
    participant O as External side effect
    C->>A: request + operation/version
    A->>B: execute domain intent
    B->>S: read/write with guard
    B->>O: idempotent side effect
    alt retry request không idempotent hoặc wrapper order sai
        O-->>B: ambiguous/transient result
        B-->>A: classified failure + evidence
    else success
        B-->>A: result preserving invariant
    end
    A-->>C: stable application response
```

## Các bước refactor

1. Định nghĩa `ApiClient` bằng ngôn ngữ của **HTTP client resilient**, không dùng interface chung chung.
2. Bọc implementation cũ sau cùng contract để tạo seam mà chưa thay behavior.
3. Thêm implementation mới và chạy dual-read/shadow compare; lưu mismatch có dữ liệu điều tra.
4. Đưa guard cho invariant **timeout/retry/logging không thay contract nghiệp vụ** gần source of truth nhất.
5. Classify **retry request không idempotent hoặc wrapper order sai** thành domain/transient/permanent và gắn retry hoặc compensation tương ứng.
6. Triển khai policy retry theo method, tracing và circuit breaker; chỉ chuyển traffic khi metric đạt ngưỡng và rollback đã được diễn tập.

## Phác thảo contract

```php
interface ApiClient
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **HTTP client resilient**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Decorator phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `HttpClientResilientBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **timeout/retry/logging không thay contract nghiệp vụ.** trên output/state, không assert tên concrete class.
- `HttpClientResilientFailureTest`: tạo **retry request không idempotent hoặc wrapper order sai.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `DecoratorContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `HttpClientResilientReplayAndConcurrencyTest`: gửi lại operation hoặc tạo race tại source of truth; assert idempotency/version guard thay vì chỉ mock call count.
- `HttpClientResilientMigrationTest`: chạy old/new trên cùng fixture hoặc shadow input, lưu mismatch có correlation ID và kiểm tra rollback trigger.
- Telemetry assertion: log/metric phải chứa operation, decision/version và failure class đủ để điều tra **HTTP client resilient**.
## Failure walkthrough

Khi **retry request không idempotent hoặc wrapper order sai**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **timeout/retry/logging không thay contract nghiệp vụ**. Nếu side effect có thể đã thành công, lần retry phải dựa vào operation record/idempotency evidence thay vì đoán.

## Trade-off và phương án thay thế

Trong **HTTP client resilient**, Decorator chỉ đáng giữ khi nó giảm rủi ro của **retry request không idempotent hoặc wrapper order sai.** hoặc cho phép migration/rollback có evidence. Chi phí thật gồm wiring, version compatibility, telemetry, runbook và ownership khi on-call — không chỉ số class.

Baseline cần so sánh là **một service duy nhất khi behavior không composable**. Nếu shadow comparison không cho thấy blast radius, correctness hoặc recovery tốt hơn, hãy giữ baseline và ghi lại trigger xem xét lại. Trước khi rollout cần xác định source of truth, cleanup condition cho implementation cũ và metric chứng minh invariant **timeout/retry/logging không thay contract nghiệp vụ.** tiếp tục được giữ.
## Dấu hiệu lời giải chưa đạt

- Boundary của Decorator không dùng ngôn ngữ **HTTP client resilient**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **timeout/retry/logging không thay contract nghiệp vụ** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Migration không có shadow evidence/rollback, hoặc retry vẫn có thể tạo side effect kép.

## Câu hỏi mở rộng

- Với **HTTP client resilient**, metric nào chứng minh Decorator giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Decorator

Trong **Lời giải tham khảo — Decorator (Production)** cấp Production, mỗi Decorator giữ nguyên contract và bọc đúng một component; hãy test thứ tự wrapper vì validation, idempotency, retry, cache và logging có thể đổi call count hoặc semantics.

### Test focus

Ở cấp **Production**, test wrapper order, exactly-once observable effect và exception propagation. Thêm failure/concurrency hoặc retry case, telemetry assertion và recovery verification.

### Bằng chứng nên lưu

Với **HTTP client resilient**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Decorator. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
