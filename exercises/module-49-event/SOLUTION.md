# Lời giải tham khảo — Domain Event (Production)

## Kết luận thiết kế

Lời giải chọn `EventPublisher` làm boundary vì nó bao quanh phần thay đổi của **Integration event publishing** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **publish sau commit và schema compatible**, không phải chứng minh rằng mọi bài toán đều cần Domain Event.

## Sơ đồ lời giải

```mermaid
sequenceDiagram
    participant C as Client
    participant A as Integration event publishing
    participant B as EventPublisher
    participant S as Source of truth
    participant O as External side effect
    C->>A: request + operation/version
    A->>B: execute domain intent
    B->>S: read/write with guard
    B->>O: idempotent side effect
    alt database commit thành công nhưng publish thất bại
        O-->>B: ambiguous/transient result
        B-->>A: classified failure + evidence
    else success
        B-->>A: result preserving invariant
    end
    A-->>C: stable application response
```

## Các bước refactor

1. Định nghĩa `EventPublisher` bằng ngôn ngữ của **Integration event publishing**, không dùng interface chung chung.
2. Bọc implementation cũ sau cùng contract để tạo seam mà chưa thay behavior.
3. Thêm implementation mới và chạy dual-read/shadow compare; lưu mismatch có dữ liệu điều tra.
4. Đưa guard cho invariant **publish sau commit và schema compatible** gần source of truth nhất.
5. Classify **database commit thành công nhưng publish thất bại** thành domain/transient/permanent và gắn retry hoặc compensation tương ứng.
6. Triển khai transactional outbox, schema registry và consumer contract; chỉ chuyển traffic khi metric đạt ngưỡng và rollback đã được diễn tập.

## Phác thảo contract

```php
interface EventPublisher
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Integration event publishing**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Domain Event phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `IntegrationEventPublishingBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **publish sau commit và schema compatible.** trên output/state, không assert tên concrete class.
- `IntegrationEventPublishingFailureTest`: tạo **database commit thành công nhưng publish thất bại.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `DomainEventContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `IntegrationEventPublishingReplayAndConcurrencyTest`: gửi lại operation hoặc tạo race tại source of truth; assert idempotency/version guard thay vì chỉ mock call count.
- `IntegrationEventPublishingMigrationTest`: chạy old/new trên cùng fixture hoặc shadow input, lưu mismatch có correlation ID và kiểm tra rollback trigger.
- Telemetry assertion: log/metric phải chứa operation, decision/version và failure class đủ để điều tra **Integration event publishing**.
## Failure walkthrough

Khi **database commit thành công nhưng publish thất bại**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **publish sau commit và schema compatible**. Nếu side effect có thể đã thành công, lần retry phải dựa vào operation record/idempotency evidence thay vì đoán.

## Trade-off và phương án thay thế

Trong **Integration event publishing**, Domain Event chỉ đáng giữ khi nó giảm rủi ro của **database commit thành công nhưng publish thất bại.** hoặc cho phép migration/rollback có evidence. Chi phí thật gồm wiring, version compatibility, telemetry, runbook và ownership khi on-call — không chỉ số class.

Baseline cần so sánh là **gọi method trực tiếp cho collaboration nội bộ đồng bộ**. Nếu shadow comparison không cho thấy blast radius, correctness hoặc recovery tốt hơn, hãy giữ baseline và ghi lại trigger xem xét lại. Trước khi rollout cần xác định source of truth, cleanup condition cho implementation cũ và metric chứng minh invariant **publish sau commit và schema compatible.** tiếp tục được giữ.
## Dấu hiệu lời giải chưa đạt

- Boundary của Domain Event không dùng ngôn ngữ **Integration event publishing**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **publish sau commit và schema compatible** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Migration không có shadow evidence/rollback, hoặc retry vẫn có thể tạo side effect kép.

## Câu hỏi mở rộng

- Với **Integration event publishing**, metric nào chứng minh Domain Event giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Domain Event

Trong **Lời giải tham khảo — Domain Event (Production)** (Production), domain event ở past tense, immutable và chỉ mô tả fact cần thiết; integration event ra ngoài bounded context cần schema/version cùng compatibility policy riêng.

### Test focus

Ở cấp **Production**, test event creation, after-commit publish, schema compatibility và consumer dedup. Thêm failure/concurrency hoặc retry case, telemetry assertion và recovery verification.

### Bằng chứng nên lưu

Với **Integration event publishing**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Domain Event. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
