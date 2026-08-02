# Lời giải tham khảo — Query Object (Foundation)

## Kết luận thiết kế

Lời giải chọn `CustomerSearch` làm boundary vì nó bao quanh phần thay đổi của **Tìm khách hàng theo bộ lọc** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **filter/sort/page có semantics rõ và ổn định**, không phải chứng minh rằng mọi bài toán đều cần Query Object.

## Sơ đồ lời giải

```mermaid
classDiagram
    class CustomerController {
      +handle(input)
    }
    class CustomerSearch {
      <<interface>>
      +apply(input)
    }
    class CustomerSearchQuery
    class RevenueReportQuery
    CustomerController --> CustomerSearch : depends on
    CustomerSearch <|.. CustomerSearchQuery
    CustomerSearch <|.. RevenueReportQuery
```

## Các bước refactor

1. Viết test cho `CustomerController` trước khi tách; test mô tả outcome chứ không khóa internal call.
2. Tách phần thay đổi thành `CustomerSearch` với input/output cụ thể của **Tìm khách hàng theo bộ lọc**.
3. Di chuyển một nhánh sang implementation đầu tiên; giữ validation dùng chung tại nơi ổn định.
4. Thay branch selection bằng dependency injection/composition root nhỏ.
5. Thêm biến thể **thêm filter lastPurchaseAt** và chạy cùng contract test.
6. So sánh với phương án đơn giản hơn: **query inline ngắn, chỉ dùng một nơi**; ghi khi nào nên xóa abstraction.

## Phác thảo contract

```php
interface CustomerSearch
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Tìm khách hàng theo bộ lọc**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Query Object phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `TimKhachHangTheoBoLocBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **filter/sort/page có semantics rõ và ổn định.** trên output/state, không assert tên concrete class.
- `TimKhachHangTheoBoLocFailureTest`: tạo **filter combination sai hoặc pagination lệch.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `Foundation:QueryObjectContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `ExtensionTest`: thêm một biến thể hợp lệ của Foundation: Query Object mà không sửa client/use case; nếu phải sửa, boundary chưa cô lập đúng change axis.
- Một test phản chứng cho phương án đơn giản hơn để chứng minh pattern mang giá trị, không chỉ tăng số type.
## Failure walkthrough

Khi **filter combination sai hoặc pagination lệch**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **filter/sort/page có semantics rõ và ổn định**. Client test error theo semantics, không phụ thuộc message của SDK hoặc exception hạ tầng.

## Trade-off và phương án thay thế

Foundation: Query Object làm change axis của **Tìm khách hàng theo bộ lọc** rõ hơn, đổi lại người học phải hiểu thêm contract, wiring và cách chọn implementation. Giá trị phải được chứng minh bằng việc thêm biến thể hoặc test failure mà client không đổi.

Hãy ưu tiên **query inline ngắn, chỉ dùng một nơi** nếu bài toán chỉ có một behavior ổn định, chưa có boundary ngoài hoặc test trực tiếp đã đủ rõ. Pattern không phải mục tiêu; mục tiêu là giữ invariant **filter/sort/page có semantics rõ và ổn định.** với thiết kế dễ đọc nhất.
## Dấu hiệu lời giải chưa đạt

- Boundary của Query Object không dùng ngôn ngữ **Tìm khách hàng theo bộ lọc**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **filter/sort/page có semantics rõ và ổn định** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Thêm biến thể mới vẫn phải sửa logic trung tâm, chứng tỏ extension point đặt sai.

## Câu hỏi mở rộng

- Với **Tìm khách hàng theo bộ lọc**, metric nào chứng minh Query Object giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Query Object

Trong **Lời giải tham khảo — Query Object (Foundation)** (Foundation), Query Object diễn đạt read concern bằng filter, sort, cursor/page và projection; nó không trả aggregate write model hoặc giả làm Repository domain.

### Test focus

Ở cấp **Foundation**, test filter combinations, stable ordering, cursor/page boundary và query budget. Giữ test tại process boundary nhỏ nhất và ưu tiên semantics dễ đọc.

### Bằng chứng nên lưu

Với **Tìm khách hàng theo bộ lọc**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Query Object. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
