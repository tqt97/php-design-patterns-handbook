# Lời giải tham khảo — Specification (Foundation)

## Kết luận thiết kế

Lời giải chọn `Specification` làm boundary vì nó bao quanh phần thay đổi của **Điều kiện nhận ưu đãi** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **rule tổ hợp trả reason nhất quán**, không phải chứng minh rằng mọi bài toán đều cần Specification.

## Sơ đồ lời giải

```mermaid
classDiagram
    class DiscountService {
      +handle(input)
    }
    class Specification {
      <<interface>>
      +apply(input)
    }
    class ActiveCustomerSpecification
    class CreditLimitSpecification
    DiscountService --> Specification : depends on
    Specification <|.. ActiveCustomerSpecification
    Specification <|.. CreditLimitSpecification
```

## Các bước refactor

1. Viết test cho `DiscountService` trước khi tách; test mô tả outcome chứ không khóa internal call.
2. Tách phần thay đổi thành `Specification` với input/output cụ thể của **Điều kiện nhận ưu đãi**.
3. Di chuyển một nhánh sang implementation đầu tiên; giữ validation dùng chung tại nơi ổn định.
4. Thay branch selection bằng dependency injection/composition root nhỏ.
5. Thêm biến thể **thêm VIP AND Active AND NotBlocked** và chạy cùng contract test.
6. So sánh với phương án đơn giản hơn: **predicate inline khi rule không tái sử dụng**; ghi khi nào nên xóa abstraction.

## Phác thảo contract

```php
interface Specification
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Điều kiện nhận ưu đãi**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Specification phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `IeuKienNhanUuAiBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **rule tổ hợp trả reason nhất quán.** trên output/state, không assert tên concrete class.
- `IeuKienNhanUuAiFailureTest`: tạo **rule mâu thuẫn hoặc null semantics.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `Foundation:SpecificationContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `ExtensionTest`: thêm một biến thể hợp lệ của Foundation: Specification mà không sửa client/use case; nếu phải sửa, boundary chưa cô lập đúng change axis.
- Một test phản chứng cho phương án đơn giản hơn để chứng minh pattern mang giá trị, không chỉ tăng số type.
## Failure walkthrough

Khi **rule mâu thuẫn hoặc null semantics**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **rule tổ hợp trả reason nhất quán**. Client test error theo semantics, không phụ thuộc message của SDK hoặc exception hạ tầng.

## Trade-off và phương án thay thế

Foundation: Specification làm change axis của **Điều kiện nhận ưu đãi** rõ hơn, đổi lại người học phải hiểu thêm contract, wiring và cách chọn implementation. Giá trị phải được chứng minh bằng việc thêm biến thể hoặc test failure mà client không đổi.

Hãy ưu tiên **predicate inline khi rule không tái sử dụng** nếu bài toán chỉ có một behavior ổn định, chưa có boundary ngoài hoặc test trực tiếp đã đủ rõ. Pattern không phải mục tiêu; mục tiêu là giữ invariant **rule tổ hợp trả reason nhất quán.** với thiết kế dễ đọc nhất.
## Dấu hiệu lời giải chưa đạt

- Boundary của Specification không dùng ngôn ngữ **Điều kiện nhận ưu đãi**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **rule tổ hợp trả reason nhất quán** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Thêm biến thể mới vẫn phải sửa logic trung tâm, chứng tỏ extension point đặt sai.

## Câu hỏi mở rộng

- Với **Điều kiện nhận ưu đãi**, metric nào chứng minh Specification giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Specification

Trong **Lời giải tham khảo — Specification (Foundation)** cấp Foundation, Specification đóng gói predicate có thể kết hợp; kết quả cần reason/explanation và semantics rõ khi thiếu dữ kiện hoặc rule không áp dụng.

### Test focus

Ở cấp **Foundation**, truth-table test cho AND/OR/NOT, versioned facts và reason output. Giữ test tại process boundary nhỏ nhất và ưu tiên semantics dễ đọc.

### Bằng chứng nên lưu

Với **Điều kiện nhận ưu đãi**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Specification. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
