# Lời giải tham khảo — Pipeline (Production)

## Kết luận thiết kế

Lời giải chọn `CheckoutPipeline` làm boundary vì nó bao quanh phần thay đổi của **Checkout processing pipeline** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **stage idempotent hoặc có compensation rõ**, không phải chứng minh rằng mọi bài toán đều cần Pipeline.

## Sơ đồ lời giải

```mermaid
sequenceDiagram
    participant C as Client
    participant A as Checkout processing pipeline
    participant B as CheckoutPipeline
    participant S as Source of truth
    participant O as External side effect
    C->>A: request + operation/version
    A->>B: execute domain intent
    B->>S: read/write with guard
    B->>O: idempotent side effect
    alt retry từ giữa pipeline tạo side effect kép
        O-->>B: ambiguous/transient result
        B-->>A: classified failure + evidence
    else success
        B-->>A: result preserving invariant
    end
    A-->>C: stable application response
```

## Các bước refactor

1. Định nghĩa `CheckoutPipeline` bằng ngôn ngữ của **Checkout processing pipeline**, không dùng interface chung chung.
2. Bọc implementation cũ sau cùng contract để tạo seam mà chưa thay behavior.
3. Thêm implementation mới và chạy dual-read/shadow compare; lưu mismatch có dữ liệu điều tra.
4. Đưa guard cho invariant **stage idempotent hoặc có compensation rõ** gần source of truth nhất.
5. Classify **retry từ giữa pipeline tạo side effect kép** thành domain/transient/permanent và gắn retry hoặc compensation tương ứng.
6. Triển khai checkpoint, stage telemetry và resume policy; chỉ chuyển traffic khi metric đạt ngưỡng và rollback đã được diễn tập.

## Phác thảo contract

```php
interface CheckoutPipeline
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Checkout processing pipeline**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Pipeline phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `CheckoutProcessingPipelineBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **stage idempotent hoặc có compensation rõ.** trên output/state, không assert tên concrete class.
- `CheckoutProcessingPipelineFailureTest`: tạo **retry từ giữa pipeline tạo side effect kép.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `PipelineContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `CheckoutProcessingPipelineReplayAndConcurrencyTest`: gửi lại operation hoặc tạo race tại source of truth; assert idempotency/version guard thay vì chỉ mock call count.
- `CheckoutProcessingPipelineMigrationTest`: chạy old/new trên cùng fixture hoặc shadow input, lưu mismatch có correlation ID và kiểm tra rollback trigger.
- Telemetry assertion: log/metric phải chứa operation, decision/version và failure class đủ để điều tra **Checkout processing pipeline**.
## Failure walkthrough

Khi **retry từ giữa pipeline tạo side effect kép**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **stage idempotent hoặc có compensation rõ**. Nếu side effect có thể đã thành công, lần retry phải dựa vào operation record/idempotency evidence thay vì đoán.

## Trade-off và phương án thay thế

Trong **Checkout processing pipeline**, Pipeline chỉ đáng giữ khi nó giảm rủi ro của **retry từ giữa pipeline tạo side effect kép.** hoặc cho phép migration/rollback có evidence. Chi phí thật gồm wiring, version compatibility, telemetry, runbook và ownership khi on-call — không chỉ số class.

Baseline cần so sánh là **loop trực tiếp khi pipeline không tái cấu hình**. Nếu shadow comparison không cho thấy blast radius, correctness hoặc recovery tốt hơn, hãy giữ baseline và ghi lại trigger xem xét lại. Trước khi rollout cần xác định source of truth, cleanup condition cho implementation cũ và metric chứng minh invariant **stage idempotent hoặc có compensation rõ.** tiếp tục được giữ.
## Dấu hiệu lời giải chưa đạt

- Boundary của Pipeline không dùng ngôn ngữ **Checkout processing pipeline**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **stage idempotent hoặc có compensation rõ** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Migration không có shadow evidence/rollback, hoặc retry vẫn có thể tạo side effect kép.

## Câu hỏi mở rộng

- Với **Checkout processing pipeline**, metric nào chứng minh Pipeline giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Pipeline

Ở production, stage contract và ordering là semantics công khai; side-effect stage cần idempotency, checkpoint, timeout budget và khả năng resume.

### Test focus

Ở cấp **Production**, test order, short-circuit, exception propagation, resume và stage telemetry. Thêm failure/concurrency hoặc retry case, telemetry assertion và recovery verification.

### Bằng chứng nên lưu

Với **Checkout processing pipeline**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Pipeline. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
