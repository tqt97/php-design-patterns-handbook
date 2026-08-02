# Lời giải tham khảo — Command (Production)

## Kết luận thiết kế

Lời giải chọn `CommandBus` làm boundary vì nó bao quanh phần thay đổi của **Command bus đa worker** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **mỗi command có handler duy nhất và idempotency key**, không phải chứng minh rằng mọi bài toán đều cần Command.

## Sơ đồ lời giải

```mermaid
sequenceDiagram
    participant C as Client
    participant A as Command bus đa worker
    participant B as CommandBus
    participant S as Source of truth
    participant O as External side effect
    C->>A: request + operation/version
    A->>B: execute domain intent
    B->>S: read/write with guard
    B->>O: idempotent side effect
    alt duplicate delivery hoặc handler version mismatch
        O-->>B: ambiguous/transient result
        B-->>A: classified failure + evidence
    else success
        B-->>A: result preserving invariant
    end
    A-->>C: stable application response
```

## Các bước refactor

1. Định nghĩa `CommandBus` bằng ngôn ngữ của **Command bus đa worker**, không dùng interface chung chung.
2. Bọc implementation cũ sau cùng contract để tạo seam mà chưa thay behavior.
3. Thêm implementation mới và chạy dual-read/shadow compare; lưu mismatch có dữ liệu điều tra.
4. Đưa guard cho invariant **mỗi command có handler duy nhất và idempotency key** gần source of truth nhất.
5. Classify **duplicate delivery hoặc handler version mismatch** thành domain/transient/permanent và gắn retry hoặc compensation tương ứng.
6. Triển khai inbox, retry classification và trace command lifecycle; chỉ chuyển traffic khi metric đạt ngưỡng và rollback đã được diễn tập.

## Phác thảo contract

```php
interface CommandBus
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Command bus đa worker**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Command phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `CommandBusAWorkerBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **mỗi command có handler duy nhất và idempotency key.** trên output/state, không assert tên concrete class.
- `CommandBusAWorkerFailureTest`: tạo **duplicate delivery hoặc handler version mismatch.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `CommandContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `CommandBusAWorkerReplayAndConcurrencyTest`: gửi lại operation hoặc tạo race tại source of truth; assert idempotency/version guard thay vì chỉ mock call count.
- `CommandBusAWorkerMigrationTest`: chạy old/new trên cùng fixture hoặc shadow input, lưu mismatch có correlation ID và kiểm tra rollback trigger.
- Telemetry assertion: log/metric phải chứa operation, decision/version và failure class đủ để điều tra **Command bus đa worker**.
## Failure walkthrough

Khi **duplicate delivery hoặc handler version mismatch**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **mỗi command có handler duy nhất và idempotency key**. Nếu side effect có thể đã thành công, lần retry phải dựa vào operation record/idempotency evidence thay vì đoán.

## Trade-off và phương án thay thế

Trong **Command bus đa worker**, Command chỉ đáng giữ khi nó giảm rủi ro của **duplicate delivery hoặc handler version mismatch.** hoặc cho phép migration/rollback có evidence. Chi phí thật gồm wiring, version compatibility, telemetry, runbook và ownership khi on-call — không chỉ số class.

Baseline cần so sánh là **method call trực tiếp khi không cần queue/history**. Nếu shadow comparison không cho thấy blast radius, correctness hoặc recovery tốt hơn, hãy giữ baseline và ghi lại trigger xem xét lại. Trước khi rollout cần xác định source of truth, cleanup condition cho implementation cũ và metric chứng minh invariant **mỗi command có handler duy nhất và idempotency key.** tiếp tục được giữ.
## Dấu hiệu lời giải chưa đạt

- Boundary của Command không dùng ngôn ngữ **Command bus đa worker**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **mỗi command có handler duy nhất và idempotency key** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Migration không có shadow evidence/rollback, hoặc retry vẫn có thể tạo side effect kép.

## Câu hỏi mở rộng

- Với **Command bus đa worker**, metric nào chứng minh Command giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Command

Ở **Lời giải tham khảo — Command (Production)** cấp Production, Command phải diễn đạt intent có handler/use-case rõ; nếu không cần dispatch, audit, retry hoặc history thì DTO trực tiếp có thể đơn giản hơn.

### Test focus

Ở cấp **Production**, test authorization, idempotency, handler uniqueness và command result/error. Thêm failure/concurrency hoặc retry case, telemetry assertion và recovery verification.

### Bằng chứng nên lưu

Với **Command bus đa worker**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Command. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
