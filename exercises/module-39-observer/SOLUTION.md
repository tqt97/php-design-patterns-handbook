# Lời giải tham khảo — Observer (Production)

## Kết luận thiết kế

Lời giải chọn `EventDispatcher` làm boundary vì nó bao quanh phần thay đổi của **Event-driven projection** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **projection eventually consistent và replayable**, không phải chứng minh rằng mọi bài toán đều cần Observer.

## Sơ đồ lời giải

```mermaid
sequenceDiagram
    participant C as Client
    participant A as Event-driven projection
    participant B as EventDispatcher
    participant S as Source of truth
    participant O as External side effect
    C->>A: request + operation/version
    A->>B: execute domain intent
    B->>S: read/write with guard
    B->>O: idempotent side effect
    alt duplicate/out-of-order event
        O-->>B: ambiguous/transient result
        B-->>A: classified failure + evidence
    else success
        B-->>A: result preserving invariant
    end
    A-->>C: stable application response
```

## Các bước refactor

1. Định nghĩa `EventDispatcher` bằng ngôn ngữ của **Event-driven projection**, không dùng interface chung chung.
2. Bọc implementation cũ sau cùng contract để tạo seam mà chưa thay behavior.
3. Thêm implementation mới và chạy dual-read/shadow compare; lưu mismatch có dữ liệu điều tra.
4. Đưa guard cho invariant **projection eventually consistent và replayable** gần source of truth nhất.
5. Classify **duplicate/out-of-order event** thành domain/transient/permanent và gắn retry hoặc compensation tương ứng.
6. Triển khai outbox, inbox, checkpoint và rebuild runbook; chỉ chuyển traffic khi metric đạt ngưỡng và rollback đã được diễn tập.

## Phác thảo contract

```php
interface EventDispatcher
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Event-driven projection**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Observer phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `EventDrivenProjectionBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **projection eventually consistent và replayable.** trên output/state, không assert tên concrete class.
- `EventDrivenProjectionFailureTest`: tạo **duplicate/out-of-order event.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `ObserverContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `EventDrivenProjectionReplayAndConcurrencyTest`: gửi lại operation hoặc tạo race tại source of truth; assert idempotency/version guard thay vì chỉ mock call count.
- `EventDrivenProjectionMigrationTest`: chạy old/new trên cùng fixture hoặc shadow input, lưu mismatch có correlation ID và kiểm tra rollback trigger.
- Telemetry assertion: log/metric phải chứa operation, decision/version và failure class đủ để điều tra **Event-driven projection**.
## Failure walkthrough

Khi **duplicate/out-of-order event**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **projection eventually consistent và replayable**. Nếu side effect có thể đã thành công, lần retry phải dựa vào operation record/idempotency evidence thay vì đoán.

## Trade-off và phương án thay thế

Trong **Event-driven projection**, Observer chỉ đáng giữ khi nó giảm rủi ro của **duplicate/out-of-order event.** hoặc cho phép migration/rollback có evidence. Chi phí thật gồm wiring, version compatibility, telemetry, runbook và ownership khi on-call — không chỉ số class.

Baseline cần so sánh là **gọi trực tiếp khi side effect bắt buộc đồng bộ**. Nếu shadow comparison không cho thấy blast radius, correctness hoặc recovery tốt hơn, hãy giữ baseline và ghi lại trigger xem xét lại. Trước khi rollout cần xác định source of truth, cleanup condition cho implementation cũ và metric chứng minh invariant **projection eventually consistent và replayable.** tiếp tục được giữ.
## Dấu hiệu lời giải chưa đạt

- Boundary của Observer không dùng ngôn ngữ **Event-driven projection**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **projection eventually consistent và replayable** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Migration không có shadow evidence/rollback, hoặc retry vẫn có thể tạo side effect kép.

## Câu hỏi mở rộng

- Với **Event-driven projection**, metric nào chứng minh Observer giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Observer

Trong **Lời giải tham khảo — Observer (Production)** cấp Production, event mô tả fact đã xảy ra; subscriber async không được âm thầm thay đổi outcome transaction gốc và phải xử lý duplicate/out-of-order theo delivery contract.

### Test focus

Ở cấp **Production**, test duplicate delivery, subscriber isolation, ordering assumption và replay. Thêm failure/concurrency hoặc retry case, telemetry assertion và recovery verification.

### Bằng chứng nên lưu

Với **Event-driven projection**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Observer. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
