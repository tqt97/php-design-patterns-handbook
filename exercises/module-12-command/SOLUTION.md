# Lời giải tham khảo — Command (Foundation)

## Kết luận thiết kế

Lời giải chọn `Command` làm boundary vì nó bao quanh phần thay đổi của **Hoàn tác thao tác đơn hàng** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **command diễn đạt intent và validate trước side effect**, không phải chứng minh rằng mọi bài toán đều cần Command.

## Sơ đồ lời giải

```mermaid
classDiagram
    class OrderController {
      +handle(input)
    }
    class Command {
      <<interface>>
      +apply(input)
    }
    class ApproveOrderCommand
    class CancelOrderCommand
    OrderController --> Command : depends on
    Command <|.. ApproveOrderCommand
    Command <|.. CancelOrderCommand
```

## Các bước refactor

1. Viết test cho `OrderController` trước khi tách; test mô tả outcome chứ không khóa internal call.
2. Tách phần thay đổi thành `Command` với input/output cụ thể của **Hoàn tác thao tác đơn hàng**.
3. Di chuyển một nhánh sang implementation đầu tiên; giữ validation dùng chung tại nơi ổn định.
4. Thay branch selection bằng dependency injection/composition root nhỏ.
5. Thêm biến thể **thêm CancelOrderCommand** và chạy cùng contract test.
6. So sánh với phương án đơn giản hơn: **method call trực tiếp khi không cần queue/history**; ghi khi nào nên xóa abstraction.

## Phác thảo contract

```php
interface Command
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Hoàn tác thao tác đơn hàng**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Command phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `HoanTacThaoTacOnHangBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **command diễn đạt intent và validate trước side effect.** trên output/state, không assert tên concrete class.
- `HoanTacThaoTacOnHangFailureTest`: tạo **handler chạy lặp hoặc thiếu authorization.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `Foundation:CommandContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `ExtensionTest`: thêm một biến thể hợp lệ của Foundation: Command mà không sửa client/use case; nếu phải sửa, boundary chưa cô lập đúng change axis.
- Một test phản chứng cho phương án đơn giản hơn để chứng minh pattern mang giá trị, không chỉ tăng số type.
## Failure walkthrough

Khi **handler chạy lặp hoặc thiếu authorization**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **command diễn đạt intent và validate trước side effect**. Client test error theo semantics, không phụ thuộc message của SDK hoặc exception hạ tầng.

## Trade-off và phương án thay thế

Foundation: Command làm change axis của **Hoàn tác thao tác đơn hàng** rõ hơn, đổi lại người học phải hiểu thêm contract, wiring và cách chọn implementation. Giá trị phải được chứng minh bằng việc thêm biến thể hoặc test failure mà client không đổi.

Hãy ưu tiên **method call trực tiếp khi không cần queue/history** nếu bài toán chỉ có một behavior ổn định, chưa có boundary ngoài hoặc test trực tiếp đã đủ rõ. Pattern không phải mục tiêu; mục tiêu là giữ invariant **command diễn đạt intent và validate trước side effect.** với thiết kế dễ đọc nhất.
## Dấu hiệu lời giải chưa đạt

- Boundary của Command không dùng ngôn ngữ **Hoàn tác thao tác đơn hàng**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **command diễn đạt intent và validate trước side effect** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Thêm biến thể mới vẫn phải sửa logic trung tâm, chứng tỏ extension point đặt sai.

## Câu hỏi mở rộng

- Với **Hoàn tác thao tác đơn hàng**, metric nào chứng minh Command giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Command

Ở **Lời giải tham khảo — Command (Foundation)** cấp Foundation, Command phải diễn đạt intent có handler/use-case rõ; nếu không cần dispatch, audit, retry hoặc history thì DTO trực tiếp có thể đơn giản hơn.

### Test focus

Ở cấp **Foundation**, test authorization, idempotency, handler uniqueness và command result/error. Giữ test tại process boundary nhỏ nhất và ưu tiên semantics dễ đọc.

### Bằng chứng nên lưu

Với **Hoàn tác thao tác đơn hàng**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Command. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
