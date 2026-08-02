# Lời giải tham khảo — Decorator (Foundation)

## Kết luận thiết kế

Lời giải chọn `Mailer` làm boundary vì nó bao quanh phần thay đổi của **Gửi email có log và retry** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **mỗi message chỉ được gửi một lần quan sát được**, không phải chứng minh rằng mọi bài toán đều cần Decorator.

## Sơ đồ lời giải

```mermaid
classDiagram
    class OrderMailer {
      +handle(input)
    }
    class Mailer {
      <<interface>>
      +apply(input)
    }
    class SmtpMailer
    class RetryingMailer
    OrderMailer --> Mailer : depends on
    Mailer <|.. SmtpMailer
    Mailer <|.. RetryingMailer
```

## Các bước refactor

1. Viết test cho `OrderMailer` trước khi tách; test mô tả outcome chứ không khóa internal call.
2. Tách phần thay đổi thành `Mailer` với input/output cụ thể của **Gửi email có log và retry**.
3. Di chuyển một nhánh sang implementation đầu tiên; giữ validation dùng chung tại nơi ổn định.
4. Thay branch selection bằng dependency injection/composition root nhỏ.
5. Thêm biến thể **thêm metrics decorator** và chạy cùng contract test.
6. So sánh với phương án đơn giản hơn: **một service duy nhất khi behavior không composable**; ghi khi nào nên xóa abstraction.

## Phác thảo contract

```php
interface Mailer
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Gửi email có log và retry**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Decorator phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `GuiEmailCoLogVaRetryBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **mỗi message chỉ được gửi một lần quan sát được.** trên output/state, không assert tên concrete class.
- `GuiEmailCoLogVaRetryFailureTest`: tạo **thứ tự wrapper gây gửi lặp.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `Foundation:DecoratorContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `ExtensionTest`: thêm một biến thể hợp lệ của Foundation: Decorator mà không sửa client/use case; nếu phải sửa, boundary chưa cô lập đúng change axis.
- Một test phản chứng cho phương án đơn giản hơn để chứng minh pattern mang giá trị, không chỉ tăng số type.
## Failure walkthrough

Khi **thứ tự wrapper gây gửi lặp**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **mỗi message chỉ được gửi một lần quan sát được**. Client test error theo semantics, không phụ thuộc message của SDK hoặc exception hạ tầng.

## Trade-off và phương án thay thế

Foundation: Decorator làm change axis của **Gửi email có log và retry** rõ hơn, đổi lại người học phải hiểu thêm contract, wiring và cách chọn implementation. Giá trị phải được chứng minh bằng việc thêm biến thể hoặc test failure mà client không đổi.

Hãy ưu tiên **một service duy nhất khi behavior không composable** nếu bài toán chỉ có một behavior ổn định, chưa có boundary ngoài hoặc test trực tiếp đã đủ rõ. Pattern không phải mục tiêu; mục tiêu là giữ invariant **mỗi message chỉ được gửi một lần quan sát được.** với thiết kế dễ đọc nhất.
## Dấu hiệu lời giải chưa đạt

- Boundary của Decorator không dùng ngôn ngữ **Gửi email có log và retry**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **mỗi message chỉ được gửi một lần quan sát được** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Thêm biến thể mới vẫn phải sửa logic trung tâm, chứng tỏ extension point đặt sai.

## Câu hỏi mở rộng

- Với **Gửi email có log và retry**, metric nào chứng minh Decorator giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Decorator

Trong **Lời giải tham khảo — Decorator (Foundation)** cấp Foundation, mỗi Decorator giữ nguyên contract và bọc đúng một component; hãy test thứ tự wrapper vì validation, idempotency, retry, cache và logging có thể đổi call count hoặc semantics.

### Test focus

Ở cấp **Foundation**, test wrapper order, exactly-once observable effect và exception propagation. Giữ test tại process boundary nhỏ nhất và ưu tiên semantics dễ đọc.

### Bằng chứng nên lưu

Với **Gửi email có log và retry**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Decorator. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
