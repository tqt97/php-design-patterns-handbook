# Lời giải tham khảo — State (Foundation)

## Kết luận thiết kế

Lời giải chọn `OrderState` làm boundary vì nó bao quanh phần thay đổi của **Vòng đời đơn hàng** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **chỉ transition hợp lệ mới thay đổi trạng thái**, không phải chứng minh rằng mọi bài toán đều cần State.

## Sơ đồ lời giải

```mermaid
classDiagram
    class Order {
      +handle(input)
    }
    class OrderState {
      <<interface>>
      +apply(input)
    }
    class PaidOrderState
    class ShippedOrderState
    Order --> OrderState : depends on
    OrderState <|.. PaidOrderState
    OrderState <|.. ShippedOrderState
```

## Các bước refactor

1. Viết test cho `Order` trước khi tách; test mô tả outcome chứ không khóa internal call.
2. Tách phần thay đổi thành `OrderState` với input/output cụ thể của **Vòng đời đơn hàng**.
3. Di chuyển một nhánh sang implementation đầu tiên; giữ validation dùng chung tại nơi ổn định.
4. Thay branch selection bằng dependency injection/composition root nhỏ.
5. Thêm biến thể **thêm Returned state** và chạy cùng contract test.
6. So sánh với phương án đơn giản hơn: **enum + switch khi transition ít và ổn định**; ghi khi nào nên xóa abstraction.

## Phác thảo contract

```php
interface OrderState
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Vòng đời đơn hàng**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của State phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `VongOiOnHangBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **chỉ transition hợp lệ mới thay đổi trạng thái.** trên output/state, không assert tên concrete class.
- `VongOiOnHangFailureTest`: tạo **cancel sau shipped hoặc transition bỏ qua guard.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `Foundation:StateContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `ExtensionTest`: thêm một biến thể hợp lệ của Foundation: State mà không sửa client/use case; nếu phải sửa, boundary chưa cô lập đúng change axis.
- Một test phản chứng cho phương án đơn giản hơn để chứng minh pattern mang giá trị, không chỉ tăng số type.
## Failure walkthrough

Khi **cancel sau shipped hoặc transition bỏ qua guard**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **chỉ transition hợp lệ mới thay đổi trạng thái**. Client test error theo semantics, không phụ thuộc message của SDK hoặc exception hạ tầng.

## Trade-off và phương án thay thế

Foundation: State làm change axis của **Vòng đời đơn hàng** rõ hơn, đổi lại người học phải hiểu thêm contract, wiring và cách chọn implementation. Giá trị phải được chứng minh bằng việc thêm biến thể hoặc test failure mà client không đổi.

Hãy ưu tiên **enum + switch khi transition ít và ổn định** nếu bài toán chỉ có một behavior ổn định, chưa có boundary ngoài hoặc test trực tiếp đã đủ rõ. Pattern không phải mục tiêu; mục tiêu là giữ invariant **chỉ transition hợp lệ mới thay đổi trạng thái.** với thiết kế dễ đọc nhất.
## Dấu hiệu lời giải chưa đạt

- Boundary của State không dùng ngôn ngữ **Vòng đời đơn hàng**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **chỉ transition hợp lệ mới thay đổi trạng thái** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Thêm biến thể mới vẫn phải sửa logic trung tâm, chứng tỏ extension point đặt sai.

## Câu hỏi mở rộng

- Với **Vòng đời đơn hàng**, metric nào chứng minh State giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho State

State object sở hữu behavior theo lifecycle; aggregate vẫn giữ invariant và transition authority.

### Test focus

Ở cấp **Foundation**, test transition table, illegal transition, concurrent version và terminal state. Giữ test tại process boundary nhỏ nhất và ưu tiên semantics dễ đọc.

### Bằng chứng nên lưu

Với **Vòng đời đơn hàng**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của State. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
