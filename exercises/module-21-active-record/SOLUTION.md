# Lời giải tham khảo — Active Record (Foundation)

## Kết luận thiết kế

Lời giải chọn `ActiveRecord` làm boundary vì nó bao quanh phần thay đổi của **CRUD quản lý ghi chú** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **save/delete giữ validation cơ bản**, không phải chứng minh rằng mọi bài toán đều cần Active Record.

## Sơ đồ lời giải

```mermaid
classDiagram
    class Note {
      +handle(input)
    }
    class ActiveRecord {
      <<interface>>
      +apply(input)
    }
    class CustomerRecord
    class InvoiceRecord
    Note --> ActiveRecord : depends on
    ActiveRecord <|.. CustomerRecord
    ActiveRecord <|.. InvoiceRecord
```

## Các bước refactor

1. Viết test cho `Note` trước khi tách; test mô tả outcome chứ không khóa internal call.
2. Tách phần thay đổi thành `ActiveRecord` với input/output cụ thể của **CRUD quản lý ghi chú**.
3. Di chuyển một nhánh sang implementation đầu tiên; giữ validation dùng chung tại nơi ổn định.
4. Thay branch selection bằng dependency injection/composition root nhỏ.
5. Thêm biến thể **thêm scope archived** và chạy cùng contract test.
6. So sánh với phương án đơn giản hơn: **Data Mapper khi domain phức tạp**; ghi khi nào nên xóa abstraction.

## Phác thảo contract

```php
interface ActiveRecord
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **CRUD quản lý ghi chú**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Active Record phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `CrudQuanLyGhiChuBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **save/delete giữ validation cơ bản.** trên output/state, không assert tên concrete class.
- `CrudQuanLyGhiChuFailureTest`: tạo **model phình to vì workflow nghiệp vụ.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `Foundation:ActiveRecordContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `ExtensionTest`: thêm một biến thể hợp lệ của Foundation: Active Record mà không sửa client/use case; nếu phải sửa, boundary chưa cô lập đúng change axis.
- Một test phản chứng cho phương án đơn giản hơn để chứng minh pattern mang giá trị, không chỉ tăng số type.
## Failure walkthrough

Khi **model phình to vì workflow nghiệp vụ**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **save/delete giữ validation cơ bản**. Client test error theo semantics, không phụ thuộc message của SDK hoặc exception hạ tầng.

## Trade-off và phương án thay thế

Foundation: Active Record làm change axis của **CRUD quản lý ghi chú** rõ hơn, đổi lại người học phải hiểu thêm contract, wiring và cách chọn implementation. Giá trị phải được chứng minh bằng việc thêm biến thể hoặc test failure mà client không đổi.

Hãy ưu tiên **Data Mapper khi domain phức tạp** nếu bài toán chỉ có một behavior ổn định, chưa có boundary ngoài hoặc test trực tiếp đã đủ rõ. Pattern không phải mục tiêu; mục tiêu là giữ invariant **save/delete giữ validation cơ bản.** với thiết kế dễ đọc nhất.
## Dấu hiệu lời giải chưa đạt

- Boundary của Active Record không dùng ngôn ngữ **CRUD quản lý ghi chú**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **save/delete giữ validation cơ bản** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Thêm biến thể mới vẫn phải sửa logic trung tâm, chứng tỏ extension point đặt sai.

## Câu hỏi mở rộng

- Với **CRUD quản lý ghi chú**, metric nào chứng minh Active Record giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Active Record

Ở bài **Lời giải tham khảo — Active Record (Foundation)** cấp Foundation, Active Record chỉ phù hợp khi lifecycle bám sát một bảng và workflow còn nhỏ; nếu rule xuyên aggregate, transaction hoặc external side effect tăng lên, hãy tách application/domain service trước khi record trở thành God Object.

### Test focus

Ở cấp **Foundation**, test validation, tenant scope, mass assignment và database constraint. Giữ test tại process boundary nhỏ nhất và ưu tiên semantics dễ đọc.

### Bằng chứng nên lưu

Với **CRUD quản lý ghi chú**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Active Record. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
