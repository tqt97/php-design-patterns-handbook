# Lời giải tham khảo — Middleware (Production)

## Kết luận thiết kế

Lời giải chọn `TenantKernel` làm boundary vì nó bao quanh phần thay đổi của **Multi-tenant HTTP stack** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **tenant context request-scoped và cleanup chắc chắn**, không phải chứng minh rằng mọi bài toán đều cần Middleware.

## Sơ đồ lời giải

```mermaid
sequenceDiagram
    participant C as Client
    participant A as Multi-tenant HTTP stack
    participant B as TenantKernel
    participant S as Source of truth
    participant O as External side effect
    C->>A: request + operation/version
    A->>B: execute domain intent
    B->>S: read/write with guard
    B->>O: idempotent side effect
    alt context leak, wrong ordering hoặc exception bypass cleanup
        O-->>B: ambiguous/transient result
        B-->>A: classified failure + evidence
    else success
        B-->>A: result preserving invariant
    end
    A-->>C: stable application response
```

## Các bước refactor

1. Định nghĩa `TenantKernel` bằng ngôn ngữ của **Multi-tenant HTTP stack**, không dùng interface chung chung.
2. Bọc implementation cũ sau cùng contract để tạo seam mà chưa thay behavior.
3. Thêm implementation mới và chạy dual-read/shadow compare; lưu mismatch có dữ liệu điều tra.
4. Đưa guard cho invariant **tenant context request-scoped và cleanup chắc chắn** gần source of truth nhất.
5. Classify **context leak, wrong ordering hoặc exception bypass cleanup** thành domain/transient/permanent và gắn retry hoặc compensation tương ứng.
6. Triển khai scoped container, finally cleanup và integration test; chỉ chuyển traffic khi metric đạt ngưỡng và rollback đã được diễn tập.

## Phác thảo contract

```php
interface TenantKernel
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Multi-tenant HTTP stack**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Middleware phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `MultiTenantHttpStackBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **tenant context request-scoped và cleanup chắc chắn.** trên output/state, không assert tên concrete class.
- `MultiTenantHttpStackFailureTest`: tạo **context leak, wrong ordering hoặc exception bypass cleanup.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `MiddlewareContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `MultiTenantHttpStackReplayAndConcurrencyTest`: gửi lại operation hoặc tạo race tại source of truth; assert idempotency/version guard thay vì chỉ mock call count.
- `MultiTenantHttpStackMigrationTest`: chạy old/new trên cùng fixture hoặc shadow input, lưu mismatch có correlation ID và kiểm tra rollback trigger.
- Telemetry assertion: log/metric phải chứa operation, decision/version và failure class đủ để điều tra **Multi-tenant HTTP stack**.
## Failure walkthrough

Khi **context leak, wrong ordering hoặc exception bypass cleanup**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **tenant context request-scoped và cleanup chắc chắn**. Nếu side effect có thể đã thành công, lần retry phải dựa vào operation record/idempotency evidence thay vì đoán.

## Trade-off và phương án thay thế

Trong **Multi-tenant HTTP stack**, Middleware chỉ đáng giữ khi nó giảm rủi ro của **context leak, wrong ordering hoặc exception bypass cleanup.** hoặc cho phép migration/rollback có evidence. Chi phí thật gồm wiring, version compatibility, telemetry, runbook và ownership khi on-call — không chỉ số class.

Baseline cần so sánh là **controller check trực tiếp cho endpoint nhỏ**. Nếu shadow comparison không cho thấy blast radius, correctness hoặc recovery tốt hơn, hãy giữ baseline và ghi lại trigger xem xét lại. Trước khi rollout cần xác định source of truth, cleanup condition cho implementation cũ và metric chứng minh invariant **tenant context request-scoped và cleanup chắc chắn.** tiếp tục được giữ.
## Dấu hiệu lời giải chưa đạt

- Boundary của Middleware không dùng ngôn ngữ **Multi-tenant HTTP stack**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **tenant context request-scoped và cleanup chắc chắn** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Migration không có shadow evidence/rollback, hoặc retry vẫn có thể tạo side effect kép.

## Câu hỏi mở rộng

- Với **Multi-tenant HTTP stack**, metric nào chứng minh Middleware giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Middleware

Ở production, Middleware chỉ xử lý cross-cutting concern; ordering, request-scope cleanup, tenant isolation và error mapping phải có integration test.

### Test focus

Ở cấp **Production**, test chain order, short-circuit, finally cleanup, tenant isolation và exception path. Thêm failure/concurrency hoặc retry case, telemetry assertion và recovery verification.

### Bằng chứng nên lưu

Với **Multi-tenant HTTP stack**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Middleware. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
