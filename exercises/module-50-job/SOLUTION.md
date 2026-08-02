# Lời giải tham khảo — Job (Production)

## Kết luận thiết kế

Lời giải chọn `TranscodeJob` làm boundary vì nó bao quanh phần thay đổi của **Media processing job** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **job resumable và resource bounded**, không phải chứng minh rằng mọi bài toán đều cần Job.

## Sơ đồ lời giải

```mermaid
sequenceDiagram
    participant C as Client
    participant A as Media processing job
    participant B as TranscodeJob
    participant S as Source of truth
    participant O as External side effect
    C->>A: request + operation/version
    A->>B: execute domain intent
    B->>S: read/write with guard
    B->>O: idempotent side effect
    alt poison message, timeout hoặc duplicate output
        O-->>B: ambiguous/transient result
        B-->>A: classified failure + evidence
    else success
        B-->>A: result preserving invariant
    end
    A-->>C: stable application response
```

## Các bước refactor

1. Định nghĩa `TranscodeJob` bằng ngôn ngữ của **Media processing job**, không dùng interface chung chung.
2. Bọc implementation cũ sau cùng contract để tạo seam mà chưa thay behavior.
3. Thêm implementation mới và chạy dual-read/shadow compare; lưu mismatch có dữ liệu điều tra.
4. Đưa guard cho invariant **job resumable và resource bounded** gần source of truth nhất.
5. Classify **poison message, timeout hoặc duplicate output** thành domain/transient/permanent và gắn retry hoặc compensation tương ứng.
6. Triển khai idempotent artifact key, DLQ và progress checkpoint; chỉ chuyển traffic khi metric đạt ngưỡng và rollback đã được diễn tập.

## Phác thảo contract

```php
interface TranscodeJob
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Media processing job**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Job phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `MediaProcessingJobBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **job resumable và resource bounded.** trên output/state, không assert tên concrete class.
- `MediaProcessingJobFailureTest`: tạo **poison message, timeout hoặc duplicate output.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `JobContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `MediaProcessingJobReplayAndConcurrencyTest`: gửi lại operation hoặc tạo race tại source of truth; assert idempotency/version guard thay vì chỉ mock call count.
- `MediaProcessingJobMigrationTest`: chạy old/new trên cùng fixture hoặc shadow input, lưu mismatch có correlation ID và kiểm tra rollback trigger.
- Telemetry assertion: log/metric phải chứa operation, decision/version và failure class đủ để điều tra **Media processing job**.
## Failure walkthrough

Khi **poison message, timeout hoặc duplicate output**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **job resumable và resource bounded**. Nếu side effect có thể đã thành công, lần retry phải dựa vào operation record/idempotency evidence thay vì đoán.

## Trade-off và phương án thay thế

Trong **Media processing job**, Job chỉ đáng giữ khi nó giảm rủi ro của **poison message, timeout hoặc duplicate output.** hoặc cho phép migration/rollback có evidence. Chi phí thật gồm wiring, version compatibility, telemetry, runbook và ownership khi on-call — không chỉ số class.

Baseline cần so sánh là **gọi đồng bộ khi latency nhỏ và cần kết quả ngay**. Nếu shadow comparison không cho thấy blast radius, correctness hoặc recovery tốt hơn, hãy giữ baseline và ghi lại trigger xem xét lại. Trước khi rollout cần xác định source of truth, cleanup condition cho implementation cũ và metric chứng minh invariant **job resumable và resource bounded.** tiếp tục được giữ.
## Dấu hiệu lời giải chưa đạt

- Boundary của Job không dùng ngôn ngữ **Media processing job**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **job resumable và resource bounded** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Migration không có shadow evidence/rollback, hoặc retry vẫn có thể tạo side effect kép.

## Câu hỏi mở rộng

- Với **Media processing job**, metric nào chứng minh Job giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Job

Ở production, Job là đơn vị retry/queue; payload phải bounded, versioned và serializable, kèm idempotency key, dead-letter và runbook replay.

### Test focus

Ở cấp **Production**, test crash-after-side-effect, timeout, backoff, poison message và DLQ. Thêm failure/concurrency hoặc retry case, telemetry assertion và recovery verification.

### Bằng chứng nên lưu

Với **Media processing job**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Job. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
