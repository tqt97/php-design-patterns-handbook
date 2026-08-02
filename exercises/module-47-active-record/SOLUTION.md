# Lời giải tham khảo — Active Record (Production)

## Kết luận thiết kế

Lời giải chọn `AdminRecord` làm boundary vì nó bao quanh phần thay đổi của **Admin CRUD platform** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **audit/tenant scope bắt buộc trên mọi mutation**, không phải chứng minh rằng mọi bài toán đều cần Active Record.

## Sơ đồ lời giải

```mermaid
sequenceDiagram
    participant C as Client
    participant A as Admin CRUD platform
    participant B as AdminRecord
    participant S as Source of truth
    participant O as External side effect
    C->>A: request + operation/version
    A->>B: execute domain intent
    B->>S: read/write with guard
    B->>O: idempotent side effect
    alt mass assignment hoặc tenant leak
        O-->>B: ambiguous/transient result
        B-->>A: classified failure + evidence
    else success
        B-->>A: result preserving invariant
    end
    A-->>C: stable application response
```

## Các bước refactor

1. Định nghĩa `AdminRecord` bằng ngôn ngữ của **Admin CRUD platform**, không dùng interface chung chung.
2. Bọc implementation cũ sau cùng contract để tạo seam mà chưa thay behavior.
3. Thêm implementation mới và chạy dual-read/shadow compare; lưu mismatch có dữ liệu điều tra.
4. Đưa guard cho invariant **audit/tenant scope bắt buộc trên mọi mutation** gần source of truth nhất.
5. Classify **mass assignment hoặc tenant leak** thành domain/transient/permanent và gắn retry hoặc compensation tương ứng.
6. Triển khai global scope review, policy và database constraint; chỉ chuyển traffic khi metric đạt ngưỡng và rollback đã được diễn tập.

## Phác thảo contract

```php
interface AdminRecord
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Admin CRUD platform**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Active Record phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `AdminCrudPlatformBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **audit/tenant scope bắt buộc trên mọi mutation.** trên output/state, không assert tên concrete class.
- `AdminCrudPlatformFailureTest`: tạo **mass assignment hoặc tenant leak.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `ActiveRecordContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `AdminCrudPlatformReplayAndConcurrencyTest`: gửi lại operation hoặc tạo race tại source of truth; assert idempotency/version guard thay vì chỉ mock call count.
- `AdminCrudPlatformMigrationTest`: chạy old/new trên cùng fixture hoặc shadow input, lưu mismatch có correlation ID và kiểm tra rollback trigger.
- Telemetry assertion: log/metric phải chứa operation, decision/version và failure class đủ để điều tra **Admin CRUD platform**.
## Failure walkthrough

Khi **mass assignment hoặc tenant leak**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **audit/tenant scope bắt buộc trên mọi mutation**. Nếu side effect có thể đã thành công, lần retry phải dựa vào operation record/idempotency evidence thay vì đoán.

## Trade-off và phương án thay thế

Trong **Admin CRUD platform**, Active Record chỉ đáng giữ khi nó giảm rủi ro của **mass assignment hoặc tenant leak.** hoặc cho phép migration/rollback có evidence. Chi phí thật gồm wiring, version compatibility, telemetry, runbook và ownership khi on-call — không chỉ số class.

Baseline cần so sánh là **Data Mapper khi domain phức tạp**. Nếu shadow comparison không cho thấy blast radius, correctness hoặc recovery tốt hơn, hãy giữ baseline và ghi lại trigger xem xét lại. Trước khi rollout cần xác định source of truth, cleanup condition cho implementation cũ và metric chứng minh invariant **audit/tenant scope bắt buộc trên mọi mutation.** tiếp tục được giữ.
## Dấu hiệu lời giải chưa đạt

- Boundary của Active Record không dùng ngôn ngữ **Admin CRUD platform**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **audit/tenant scope bắt buộc trên mọi mutation** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Migration không có shadow evidence/rollback, hoặc retry vẫn có thể tạo side effect kép.

## Câu hỏi mở rộng

- Với **Admin CRUD platform**, metric nào chứng minh Active Record giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Active Record

Ở bài **Lời giải tham khảo — Active Record (Production)** cấp Production, Active Record chỉ phù hợp khi lifecycle bám sát một bảng và workflow còn nhỏ; nếu rule xuyên aggregate, transaction hoặc external side effect tăng lên, hãy tách application/domain service trước khi record trở thành God Object.

### Test focus

Ở cấp **Production**, test validation, tenant scope, mass assignment và database constraint. Thêm failure/concurrency hoặc retry case, telemetry assertion và recovery verification.

### Bằng chứng nên lưu

Với **Admin CRUD platform**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Active Record. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
