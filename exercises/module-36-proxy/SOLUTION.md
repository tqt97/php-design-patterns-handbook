# Lời giải tham khảo — Proxy (Production)

## Kết luận thiết kế

Lời giải chọn `PricingClient` làm boundary vì nó bao quanh phần thay đổi của **Remote service proxy** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **authorization, timeout và cache giữ đúng scope**, không phải chứng minh rằng mọi bài toán đều cần Proxy.

## Sơ đồ lời giải

```mermaid
sequenceDiagram
    participant C as Client
    participant A as Remote service proxy
    participant B as PricingClient
    participant S as Source of truth
    participant O as External side effect
    C->>A: request + operation/version
    A->>B: execute domain intent
    B->>S: read/write with guard
    B->>O: idempotent side effect
    alt cache poisoning hoặc stale authorization
        O-->>B: ambiguous/transient result
        B-->>A: classified failure + evidence
    else success
        B-->>A: result preserving invariant
    end
    A-->>C: stable application response
```

## Các bước refactor

1. Định nghĩa `PricingClient` bằng ngôn ngữ của **Remote service proxy**, không dùng interface chung chung.
2. Bọc implementation cũ sau cùng contract để tạo seam mà chưa thay behavior.
3. Thêm implementation mới và chạy dual-read/shadow compare; lưu mismatch có dữ liệu điều tra.
4. Đưa guard cho invariant **authorization, timeout và cache giữ đúng scope** gần source of truth nhất.
5. Classify **cache poisoning hoặc stale authorization** thành domain/transient/permanent và gắn retry hoặc compensation tương ứng.
6. Triển khai tenant-aware cache, bulkhead và access audit; chỉ chuyển traffic khi metric đạt ngưỡng và rollback đã được diễn tập.

## Phác thảo contract

```php
interface PricingClient
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Remote service proxy**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Proxy phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `RemoteServiceProxyBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **authorization, timeout và cache giữ đúng scope.** trên output/state, không assert tên concrete class.
- `RemoteServiceProxyFailureTest`: tạo **cache poisoning hoặc stale authorization.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `ProxyContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `RemoteServiceProxyReplayAndConcurrencyTest`: gửi lại operation hoặc tạo race tại source of truth; assert idempotency/version guard thay vì chỉ mock call count.
- `RemoteServiceProxyMigrationTest`: chạy old/new trên cùng fixture hoặc shadow input, lưu mismatch có correlation ID và kiểm tra rollback trigger.
- Telemetry assertion: log/metric phải chứa operation, decision/version và failure class đủ để điều tra **Remote service proxy**.
## Failure walkthrough

Khi **cache poisoning hoặc stale authorization**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **authorization, timeout và cache giữ đúng scope**. Nếu side effect có thể đã thành công, lần retry phải dựa vào operation record/idempotency evidence thay vì đoán.

## Trade-off và phương án thay thế

Trong **Remote service proxy**, Proxy chỉ đáng giữ khi nó giảm rủi ro của **cache poisoning hoặc stale authorization.** hoặc cho phép migration/rollback có evidence. Chi phí thật gồm wiring, version compatibility, telemetry, runbook và ownership khi on-call — không chỉ số class.

Baseline cần so sánh là **check quyền trực tiếp khi chỉ một call site**. Nếu shadow comparison không cho thấy blast radius, correctness hoặc recovery tốt hơn, hãy giữ baseline và ghi lại trigger xem xét lại. Trước khi rollout cần xác định source of truth, cleanup condition cho implementation cũ và metric chứng minh invariant **authorization, timeout và cache giữ đúng scope.** tiếp tục được giữ.
## Dấu hiệu lời giải chưa đạt

- Boundary của Proxy không dùng ngôn ngữ **Remote service proxy**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **authorization, timeout và cache giữ đúng scope** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Migration không có shadow evidence/rollback, hoặc retry vẫn có thể tạo side effect kép.

## Câu hỏi mở rộng

- Với **Remote service proxy**, metric nào chứng minh Proxy giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Proxy

Bài **Lời giải tham khảo — Proxy (Production)** cấp Production dùng Proxy để kiểm soát authorization/remote/lazy/cache access; cache key và authorization decision phải bao gồm tenant/user/security scope để tránh data leak.

### Test focus

Ở cấp **Production**, test denied access không gọi subject, cache isolation và stale authorization. Thêm failure/concurrency hoặc retry case, telemetry assertion và recovery verification.

### Bằng chứng nên lưu

Với **Remote service proxy**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Proxy. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
