# Lời giải tham khảo — Data Mapper (Production)

## Kết luận thiết kế

Lời giải chọn `LegacyOrderMapper` làm boundary vì nó bao quanh phần thay đổi của **Legacy schema migration** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **mapping hai chiều bảo toàn money/time/status semantics**, không phải chứng minh rằng mọi bài toán đều cần Data Mapper.

## Sơ đồ lời giải

```mermaid
sequenceDiagram
    participant C as Client
    participant A as Legacy schema migration
    participant B as LegacyOrderMapper
    participant S as Source of truth
    participant O as External side effect
    C->>A: request + operation/version
    A->>B: execute domain intent
    B->>S: read/write with guard
    B->>O: idempotent side effect
    alt null legacy, timezone hoặc enum mapping sai
        O-->>B: ambiguous/transient result
        B-->>A: classified failure + evidence
    else success
        B-->>A: result preserving invariant
    end
    A-->>C: stable application response
```

## Các bước refactor

1. Định nghĩa `LegacyOrderMapper` bằng ngôn ngữ của **Legacy schema migration**, không dùng interface chung chung.
2. Bọc implementation cũ sau cùng contract để tạo seam mà chưa thay behavior.
3. Thêm implementation mới và chạy dual-read/shadow compare; lưu mismatch có dữ liệu điều tra.
4. Đưa guard cho invariant **mapping hai chiều bảo toàn money/time/status semantics** gần source of truth nhất.
5. Classify **null legacy, timezone hoặc enum mapping sai** thành domain/transient/permanent và gắn retry hoặc compensation tương ứng.
6. Triển khai golden master mapping và dual-read verification; chỉ chuyển traffic khi metric đạt ngưỡng và rollback đã được diễn tập.

## Phác thảo contract

```php
interface LegacyOrderMapper
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Legacy schema migration**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Data Mapper phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `LegacySchemaMigrationBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **mapping hai chiều bảo toàn money/time/status semantics.** trên output/state, không assert tên concrete class.
- `LegacySchemaMigrationFailureTest`: tạo **null legacy, timezone hoặc enum mapping sai.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `DataMapperContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `LegacySchemaMigrationReplayAndConcurrencyTest`: gửi lại operation hoặc tạo race tại source of truth; assert idempotency/version guard thay vì chỉ mock call count.
- `LegacySchemaMigrationMigrationTest`: chạy old/new trên cùng fixture hoặc shadow input, lưu mismatch có correlation ID và kiểm tra rollback trigger.
- Telemetry assertion: log/metric phải chứa operation, decision/version và failure class đủ để điều tra **Legacy schema migration**.
## Failure walkthrough

Khi **null legacy, timezone hoặc enum mapping sai**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **mapping hai chiều bảo toàn money/time/status semantics**. Nếu side effect có thể đã thành công, lần retry phải dựa vào operation record/idempotency evidence thay vì đoán.

## Trade-off và phương án thay thế

Trong **Legacy schema migration**, Data Mapper chỉ đáng giữ khi nó giảm rủi ro của **null legacy, timezone hoặc enum mapping sai.** hoặc cho phép migration/rollback có evidence. Chi phí thật gồm wiring, version compatibility, telemetry, runbook và ownership khi on-call — không chỉ số class.

Baseline cần so sánh là **Active Record cho ứng dụng CRUD nhỏ**. Nếu shadow comparison không cho thấy blast radius, correctness hoặc recovery tốt hơn, hãy giữ baseline và ghi lại trigger xem xét lại. Trước khi rollout cần xác định source of truth, cleanup condition cho implementation cũ và metric chứng minh invariant **mapping hai chiều bảo toàn money/time/status semantics.** tiếp tục được giữ.
## Dấu hiệu lời giải chưa đạt

- Boundary của Data Mapper không dùng ngôn ngữ **Legacy schema migration**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **mapping hai chiều bảo toàn money/time/status semantics** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Migration không có shadow evidence/rollback, hoặc retry vẫn có thể tạo side effect kép.

## Câu hỏi mở rộng

- Với **Legacy schema migration**, metric nào chứng minh Data Mapper giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Data Mapper

Bài **Lời giải tham khảo — Data Mapper (Production)** cấp Production yêu cầu Mapper tái tạo domain object mà không bypass invariant; schema evolution cần fixture/version test và chiến lược đọc dữ liệu cũ.

### Test focus

Ở cấp **Production**, test round-trip, null legacy, money/timezone/enum và schema migration. Thêm failure/concurrency hoặc retry case, telemetry assertion và recovery verification.

### Bằng chứng nên lưu

Với **Legacy schema migration**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Data Mapper. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
