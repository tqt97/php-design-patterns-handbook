# Lời giải tham khảo — Abstract Factory (Production)

## Kết luận thiết kế

Lời giải chọn `RegionalClientFactory` làm boundary vì nó bao quanh phần thay đổi của **Bộ client theo khu vực** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **serializer, signer và endpoint cùng region**, không phải chứng minh rằng mọi bài toán đều cần Abstract Factory.

## Sơ đồ lời giải

```mermaid
sequenceDiagram
    participant C as Client
    participant A as Bộ client theo khu vực
    participant B as RegionalClientFactory
    participant S as Source of truth
    participant O as External side effect
    C->>A: request + operation/version
    A->>B: execute domain intent
    B->>S: read/write with guard
    B->>O: idempotent side effect
    alt trộn signer/endpoint khác region
        O-->>B: ambiguous/transient result
        B-->>A: classified failure + evidence
    else success
        B-->>A: result preserving invariant
    end
    A-->>C: stable application response
```

## Các bước refactor

1. Định nghĩa `RegionalClientFactory` bằng ngôn ngữ của **Bộ client theo khu vực**, không dùng interface chung chung.
2. Bọc implementation cũ sau cùng contract để tạo seam mà chưa thay behavior.
3. Thêm implementation mới và chạy dual-read/shadow compare; lưu mismatch có dữ liệu điều tra.
4. Đưa guard cho invariant **serializer, signer và endpoint cùng region** gần source of truth nhất.
5. Classify **trộn signer/endpoint khác region** thành domain/transient/permanent và gắn retry hoặc compensation tương ứng.
6. Triển khai version family, rollout theo region; chỉ chuyển traffic khi metric đạt ngưỡng và rollback đã được diễn tập.

## Phác thảo contract

```php
interface RegionalClientFactory
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Bộ client theo khu vực**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Abstract Factory phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `BoClientTheoKhuVucBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **serializer, signer và endpoint cùng region.** trên output/state, không assert tên concrete class.
- `BoClientTheoKhuVucFailureTest`: tạo **trộn signer/endpoint khác region.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `AbstractFactoryContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `BoClientTheoKhuVucReplayAndConcurrencyTest`: gửi lại operation hoặc tạo race tại source of truth; assert idempotency/version guard thay vì chỉ mock call count.
- `BoClientTheoKhuVucMigrationTest`: chạy old/new trên cùng fixture hoặc shadow input, lưu mismatch có correlation ID và kiểm tra rollback trigger.
- Telemetry assertion: log/metric phải chứa operation, decision/version và failure class đủ để điều tra **Bộ client theo khu vực**.
## Failure walkthrough

Khi **trộn signer/endpoint khác region**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **serializer, signer và endpoint cùng region**. Nếu side effect có thể đã thành công, lần retry phải dựa vào operation record/idempotency evidence thay vì đoán.

## Trade-off và phương án thay thế

Trong **Bộ client theo khu vực**, Abstract Factory chỉ đáng giữ khi nó giảm rủi ro của **trộn signer/endpoint khác region.** hoặc cho phép migration/rollback có evidence. Chi phí thật gồm wiring, version compatibility, telemetry, runbook và ownership khi on-call — không chỉ số class.

Baseline cần so sánh là **tạo object trực tiếp khi không có family invariant**. Nếu shadow comparison không cho thấy blast radius, correctness hoặc recovery tốt hơn, hãy giữ baseline và ghi lại trigger xem xét lại. Trước khi rollout cần xác định source of truth, cleanup condition cho implementation cũ và metric chứng minh invariant **serializer, signer và endpoint cùng region.** tiếp tục được giữ.
## Dấu hiệu lời giải chưa đạt

- Boundary của Abstract Factory không dùng ngôn ngữ **Bộ client theo khu vực**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **serializer, signer và endpoint cùng region** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Migration không có shadow evidence/rollback, hoặc retry vẫn có thể tạo side effect kép.

## Câu hỏi mở rộng

- Với **Bộ client theo khu vực**, metric nào chứng minh Abstract Factory giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Abstract Factory

Trong **Lời giải tham khảo — Abstract Factory (Production)** cấp Production, product family cần compatibility invariant và test theo family; nếu client vẫn trộn concrete products tùy ý thì chưa chứng minh được Abstract Factory.

### Test focus

Ở cấp **Production**, test một family hoàn chỉnh và test ngăn trộn product giữa hai family. Thêm failure/concurrency hoặc retry case, telemetry assertion và recovery verification.

### Bằng chứng nên lưu

Với **Bộ client theo khu vực**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Abstract Factory. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
