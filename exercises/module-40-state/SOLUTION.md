# Lời giải tham khảo — State (Production)

## Kết luận thiết kế

Lời giải chọn `BookingState` làm boundary vì nó bao quanh phần thay đổi của **Booking lifecycle** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **hold/confirm/cancel tuân TTL và ownership**, không phải chứng minh rằng mọi bài toán đều cần State.

## Sơ đồ lời giải

```mermaid
sequenceDiagram
    participant C as Client
    participant A as Booking lifecycle
    participant B as BookingState
    participant S as Source of truth
    participant O as External side effect
    C->>A: request + operation/version
    A->>B: execute domain intent
    B->>S: read/write with guard
    B->>O: idempotent side effect
    alt race confirm-expire hoặc event đến muộn
        O-->>B: ambiguous/transient result
        B-->>A: classified failure + evidence
    else success
        B-->>A: result preserving invariant
    end
    A-->>C: stable application response
```

## Các bước refactor

1. Định nghĩa `BookingState` bằng ngôn ngữ của **Booking lifecycle**, không dùng interface chung chung.
2. Bọc implementation cũ sau cùng contract để tạo seam mà chưa thay behavior.
3. Thêm implementation mới và chạy dual-read/shadow compare; lưu mismatch có dữ liệu điều tra.
4. Đưa guard cho invariant **hold/confirm/cancel tuân TTL và ownership** gần source of truth nhất.
5. Classify **race confirm-expire hoặc event đến muộn** thành domain/transient/permanent và gắn retry hoặc compensation tương ứng.
6. Triển khai optimistic locking, transition audit và repair command; chỉ chuyển traffic khi metric đạt ngưỡng và rollback đã được diễn tập.

## Phác thảo contract

```php
interface BookingState
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Booking lifecycle**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của State phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `BookingLifecycleBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **hold/confirm/cancel tuân TTL và ownership.** trên output/state, không assert tên concrete class.
- `BookingLifecycleFailureTest`: tạo **race confirm-expire hoặc event đến muộn.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `StateContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `BookingLifecycleReplayAndConcurrencyTest`: gửi lại operation hoặc tạo race tại source of truth; assert idempotency/version guard thay vì chỉ mock call count.
- `BookingLifecycleMigrationTest`: chạy old/new trên cùng fixture hoặc shadow input, lưu mismatch có correlation ID và kiểm tra rollback trigger.
- Telemetry assertion: log/metric phải chứa operation, decision/version và failure class đủ để điều tra **Booking lifecycle**.
## Failure walkthrough

Khi **race confirm-expire hoặc event đến muộn**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **hold/confirm/cancel tuân TTL và ownership**. Nếu side effect có thể đã thành công, lần retry phải dựa vào operation record/idempotency evidence thay vì đoán.

## Trade-off và phương án thay thế

Trong **Booking lifecycle**, State chỉ đáng giữ khi nó giảm rủi ro của **race confirm-expire hoặc event đến muộn.** hoặc cho phép migration/rollback có evidence. Chi phí thật gồm wiring, version compatibility, telemetry, runbook và ownership khi on-call — không chỉ số class.

Baseline cần so sánh là **enum + switch khi transition ít và ổn định**. Nếu shadow comparison không cho thấy blast radius, correctness hoặc recovery tốt hơn, hãy giữ baseline và ghi lại trigger xem xét lại. Trước khi rollout cần xác định source of truth, cleanup condition cho implementation cũ và metric chứng minh invariant **hold/confirm/cancel tuân TTL và ownership.** tiếp tục được giữ.
## Dấu hiệu lời giải chưa đạt

- Boundary của State không dùng ngôn ngữ **Booking lifecycle**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **hold/confirm/cancel tuân TTL và ownership** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Migration không có shadow evidence/rollback, hoặc retry vẫn có thể tạo side effect kép.

## Câu hỏi mở rộng

- Với **Booking lifecycle**, metric nào chứng minh State giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho State

Ở production, State object đóng gói behavior theo lifecycle nhưng aggregate vẫn sở hữu invariant, version và transition authority; transition phải ghi audit và chống stale command.

### Test focus

Ở cấp **Production**, test transition table, illegal transition, concurrent version và terminal state. Thêm failure/concurrency hoặc retry case, telemetry assertion và recovery verification.

### Bằng chứng nên lưu

Với **Booking lifecycle**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của State. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
