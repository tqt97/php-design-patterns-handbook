# Lời giải tham khảo — Unit of Work (Production)

## Kết luận thiết kế

Lời giải chọn `ApplicationService` làm boundary vì nó bao quanh phần thay đổi của **Cross-aggregate transaction** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **transaction boundary không bao network call**, không phải chứng minh rằng mọi bài toán đều cần Unit of Work.

## Sơ đồ lời giải

```mermaid
sequenceDiagram
    participant C as Client
    participant A as Cross-aggregate transaction
    participant B as ApplicationService
    participant S as Source of truth
    participant O as External side effect
    C->>A: request + operation/version
    A->>B: execute domain intent
    B->>S: read/write with guard
    B->>O: idempotent side effect
    alt deadlock, retry hoặc nested transaction sai
        O-->>B: ambiguous/transient result
        B-->>A: classified failure + evidence
    else success
        B-->>A: result preserving invariant
    end
    A-->>C: stable application response
```

## Các bước refactor

1. Định nghĩa `ApplicationService` bằng ngôn ngữ của **Cross-aggregate transaction**, không dùng interface chung chung.
2. Bọc implementation cũ sau cùng contract để tạo seam mà chưa thay behavior.
3. Thêm implementation mới và chạy dual-read/shadow compare; lưu mismatch có dữ liệu điều tra.
4. Đưa guard cho invariant **transaction boundary không bao network call** gần source of truth nhất.
5. Classify **deadlock, retry hoặc nested transaction sai** thành domain/transient/permanent và gắn retry hoặc compensation tương ứng.
6. Triển khai retry-safe unit, after-commit outbox và lock order; chỉ chuyển traffic khi metric đạt ngưỡng và rollback đã được diễn tập.

## Phác thảo contract

```php
interface ApplicationService
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Cross-aggregate transaction**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Unit of Work phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `CrossAggregateTransactionBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **transaction boundary không bao network call.** trên output/state, không assert tên concrete class.
- `CrossAggregateTransactionFailureTest`: tạo **deadlock, retry hoặc nested transaction sai.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `UnitofWorkContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `CrossAggregateTransactionReplayAndConcurrencyTest`: gửi lại operation hoặc tạo race tại source of truth; assert idempotency/version guard thay vì chỉ mock call count.
- `CrossAggregateTransactionMigrationTest`: chạy old/new trên cùng fixture hoặc shadow input, lưu mismatch có correlation ID và kiểm tra rollback trigger.
- Telemetry assertion: log/metric phải chứa operation, decision/version và failure class đủ để điều tra **Cross-aggregate transaction**.
## Failure walkthrough

Khi **deadlock, retry hoặc nested transaction sai**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **transaction boundary không bao network call**. Nếu side effect có thể đã thành công, lần retry phải dựa vào operation record/idempotency evidence thay vì đoán.

## Trade-off và phương án thay thế

Trong **Cross-aggregate transaction**, Unit of Work chỉ đáng giữ khi nó giảm rủi ro của **deadlock, retry hoặc nested transaction sai.** hoặc cho phép migration/rollback có evidence. Chi phí thật gồm wiring, version compatibility, telemetry, runbook và ownership khi on-call — không chỉ số class.

Baseline cần so sánh là **transaction script trực tiếp khi scope nhỏ**. Nếu shadow comparison không cho thấy blast radius, correctness hoặc recovery tốt hơn, hãy giữ baseline và ghi lại trigger xem xét lại. Trước khi rollout cần xác định source of truth, cleanup condition cho implementation cũ và metric chứng minh invariant **transaction boundary không bao network call.** tiếp tục được giữ.
## Dấu hiệu lời giải chưa đạt

- Boundary của Unit of Work không dùng ngôn ngữ **Cross-aggregate transaction**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **transaction boundary không bao network call** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Migration không có shadow evidence/rollback, hoặc retry vẫn có thể tạo side effect kép.

## Câu hỏi mở rộng

- Với **Cross-aggregate transaction**, metric nào chứng minh Unit of Work giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Unit of Work

Trong **Lời giải tham khảo — Unit of Work (Production)** cấp Production, Unit of Work gom thay đổi và commit atomically; network call nằm ngoài DB transaction và cần outbox/compensation nếu phải phối hợp side effect.

### Test focus

Ở cấp **Production**, test commit, rollback, nested behavior, deadlock retry và after-commit event. Thêm failure/concurrency hoặc retry case, telemetry assertion và recovery verification.

### Bằng chứng nên lưu

Với **Cross-aggregate transaction**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Unit of Work. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
