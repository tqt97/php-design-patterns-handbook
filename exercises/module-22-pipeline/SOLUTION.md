# Lời giải tham khảo — Pipeline (Foundation)

## Kết luận thiết kế

Lời giải chọn `Stage` làm boundary vì nó bao quanh phần thay đổi của **Chuẩn hóa dữ liệu import** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **mỗi stage có input/output contract rõ**, không phải chứng minh rằng mọi bài toán đều cần Pipeline.

## Sơ đồ lời giải

```mermaid
classDiagram
    class ImportPipeline {
      +handle(input)
    }
    class Stage {
      <<interface>>
      +apply(input)
    }
    class ValidateOrderStage
    class ReserveInventoryStage
    ImportPipeline --> Stage : depends on
    Stage <|.. ValidateOrderStage
    Stage <|.. ReserveInventoryStage
```

## Các bước refactor

1. Viết test cho `ImportPipeline` trước khi tách; test mô tả outcome chứ không khóa internal call.
2. Tách phần thay đổi thành `Stage` với input/output cụ thể của **Chuẩn hóa dữ liệu import**.
3. Di chuyển một nhánh sang implementation đầu tiên; giữ validation dùng chung tại nơi ổn định.
4. Thay branch selection bằng dependency injection/composition root nhỏ.
5. Thêm biến thể **thêm DeduplicateStage** và chạy cùng contract test.
6. So sánh với phương án đơn giản hơn: **loop trực tiếp khi pipeline không tái cấu hình**; ghi khi nào nên xóa abstraction.

## Phác thảo contract

```php
interface Stage
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Chuẩn hóa dữ liệu import**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Pipeline phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `ChuanHoaDuLieuImportBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **mỗi stage có input/output contract rõ.** trên output/state, không assert tên concrete class.
- `ChuanHoaDuLieuImportFailureTest`: tạo **stage order sai hoặc stage nuốt lỗi.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `Foundation:PipelineContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `ExtensionTest`: thêm một biến thể hợp lệ của Foundation: Pipeline mà không sửa client/use case; nếu phải sửa, boundary chưa cô lập đúng change axis.
- Một test phản chứng cho phương án đơn giản hơn để chứng minh pattern mang giá trị, không chỉ tăng số type.
## Failure walkthrough

Khi **stage order sai hoặc stage nuốt lỗi**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **mỗi stage có input/output contract rõ**. Client test error theo semantics, không phụ thuộc message của SDK hoặc exception hạ tầng.

## Trade-off và phương án thay thế

Foundation: Pipeline làm change axis của **Chuẩn hóa dữ liệu import** rõ hơn, đổi lại người học phải hiểu thêm contract, wiring và cách chọn implementation. Giá trị phải được chứng minh bằng việc thêm biến thể hoặc test failure mà client không đổi.

Hãy ưu tiên **loop trực tiếp khi pipeline không tái cấu hình** nếu bài toán chỉ có một behavior ổn định, chưa có boundary ngoài hoặc test trực tiếp đã đủ rõ. Pattern không phải mục tiêu; mục tiêu là giữ invariant **mỗi stage có input/output contract rõ.** với thiết kế dễ đọc nhất.
## Dấu hiệu lời giải chưa đạt

- Boundary của Pipeline không dùng ngôn ngữ **Chuẩn hóa dữ liệu import**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **mỗi stage có input/output contract rõ** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Thêm biến thể mới vẫn phải sửa logic trung tâm, chứng tỏ extension point đặt sai.

## Câu hỏi mở rộng

- Với **Chuẩn hóa dữ liệu import**, metric nào chứng minh Pipeline giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Pipeline

Stage contract và ordering là một phần semantics. Side-effect stage cần idempotency hoặc checkpoint.

### Test focus

Ở cấp **Foundation**, test order, short-circuit, exception propagation, resume và stage telemetry. Giữ test tại process boundary nhỏ nhất và ưu tiên semantics dễ đọc.

### Bằng chứng nên lưu

Với **Chuẩn hóa dữ liệu import**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Pipeline. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
