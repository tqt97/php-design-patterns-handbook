# Lời giải tham khảo — Bridge (Production)

## Kết luận thiết kế

Lời giải chọn `Transport` làm boundary vì nó bao quanh phần thay đổi của **Thông báo đa template/transport** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **template và transport tiến hóa độc lập**, không phải chứng minh rằng mọi bài toán đều cần Bridge.

## Sơ đồ lời giải

```mermaid
sequenceDiagram
    participant C as Client
    participant A as Thông báo đa template/transport
    participant B as Transport
    participant S as Source of truth
    participant O as External side effect
    C->>A: request + operation/version
    A->>B: execute domain intent
    B->>S: read/write with guard
    B->>O: idempotent side effect
    alt capability mismatch giữa notification và transport
        O-->>B: ambiguous/transient result
        B-->>A: classified failure + evidence
    else success
        B-->>A: result preserving invariant
    end
    A-->>C: stable application response
```

## Các bước refactor

1. Định nghĩa `Transport` bằng ngôn ngữ của **Thông báo đa template/transport**, không dùng interface chung chung.
2. Bọc implementation cũ sau cùng contract để tạo seam mà chưa thay behavior.
3. Thêm implementation mới và chạy dual-read/shadow compare; lưu mismatch có dữ liệu điều tra.
4. Đưa guard cho invariant **template và transport tiến hóa độc lập** gần source of truth nhất.
5. Classify **capability mismatch giữa notification và transport** thành domain/transient/permanent và gắn retry hoặc compensation tương ứng.
6. Triển khai capability negotiation và compatibility matrix; chỉ chuyển traffic khi metric đạt ngưỡng và rollback đã được diễn tập.

## Phác thảo contract

```php
interface Transport
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Thông báo đa template/transport**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Bridge phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `ThongBaoATemplateTransportBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **template và transport tiến hóa độc lập.** trên output/state, không assert tên concrete class.
- `ThongBaoATemplateTransportFailureTest`: tạo **capability mismatch giữa notification và transport.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `BridgeContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `ThongBaoATemplateTransportReplayAndConcurrencyTest`: gửi lại operation hoặc tạo race tại source of truth; assert idempotency/version guard thay vì chỉ mock call count.
- `ThongBaoATemplateTransportMigrationTest`: chạy old/new trên cùng fixture hoặc shadow input, lưu mismatch có correlation ID và kiểm tra rollback trigger.
- Telemetry assertion: log/metric phải chứa operation, decision/version và failure class đủ để điều tra **Thông báo đa template/transport**.
## Failure walkthrough

Khi **capability mismatch giữa notification và transport**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **template và transport tiến hóa độc lập**. Nếu side effect có thể đã thành công, lần retry phải dựa vào operation record/idempotency evidence thay vì đoán.

## Trade-off và phương án thay thế

Trong **Thông báo đa template/transport**, Bridge chỉ đáng giữ khi nó giảm rủi ro của **capability mismatch giữa notification và transport.** hoặc cho phép migration/rollback có evidence. Chi phí thật gồm wiring, version compatibility, telemetry, runbook và ownership khi on-call — không chỉ số class.

Baseline cần so sánh là **kế thừa khi chỉ có một trục thay đổi**. Nếu shadow comparison không cho thấy blast radius, correctness hoặc recovery tốt hơn, hãy giữ baseline và ghi lại trigger xem xét lại. Trước khi rollout cần xác định source of truth, cleanup condition cho implementation cũ và metric chứng minh invariant **template và transport tiến hóa độc lập.** tiếp tục được giữ.
## Dấu hiệu lời giải chưa đạt

- Boundary của Bridge không dùng ngôn ngữ **Thông báo đa template/transport**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **template và transport tiến hóa độc lập** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Migration không có shadow evidence/rollback, hoặc retry vẫn có thể tạo side effect kép.

## Câu hỏi mở rộng

- Với **Thông báo đa template/transport**, metric nào chứng minh Bridge giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Bridge

Bài **Lời giải tham khảo — Bridge (Production)** cấp Production dùng Bridge khi abstraction và implementation thật sự thay đổi độc lập; hãy chứng minh tránh được tích Descartes bằng change scenario và contract test cho từng trục.

### Test focus

Ở cấp **Production**, test mọi cặp abstraction–implementation quan trọng và capability mismatch. Thêm failure/concurrency hoặc retry case, telemetry assertion và recovery verification.

### Bằng chứng nên lưu

Với **Thông báo đa template/transport**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Bridge. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
