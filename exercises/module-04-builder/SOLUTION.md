# Lời giải tham khảo — Builder (Foundation)

## Kết luận thiết kế

Lời giải chọn `ReportBuilder` làm boundary vì nó bao quanh phần thay đổi của **Tạo báo cáo nhiều phần** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **report hoàn chỉnh mới được xuất**, không phải chứng minh rằng mọi bài toán đều cần Builder.

## Sơ đồ lời giải

```mermaid
classDiagram
    class ReportDirector {
      +handle(input)
    }
    class ReportBuilder {
      <<interface>>
      +apply(input)
    }
    class ExecutiveReportBuilder
    class AuditReportBuilder
    ReportDirector --> ReportBuilder : depends on
    ReportBuilder <|.. ExecutiveReportBuilder
    ReportBuilder <|.. AuditReportBuilder
```

## Các bước refactor

1. Viết test cho `ReportDirector` trước khi tách; test mô tả outcome chứ không khóa internal call.
2. Tách phần thay đổi thành `ReportBuilder` với input/output cụ thể của **Tạo báo cáo nhiều phần**.
3. Di chuyển một nhánh sang implementation đầu tiên; giữ validation dùng chung tại nơi ổn định.
4. Thay branch selection bằng dependency injection/composition root nhỏ.
5. Thêm biến thể **thêm preset báo cáo kiểm toán** và chạy cùng contract test.
6. So sánh với phương án đơn giản hơn: **constructor named arguments khi object đơn giản**; ghi khi nào nên xóa abstraction.

## Phác thảo contract

```php
interface ReportBuilder
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Tạo báo cáo nhiều phần**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Builder phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `TaoBaoCaoNhieuPhanBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **report hoàn chỉnh mới được xuất.** trên output/state, không assert tên concrete class.
- `TaoBaoCaoNhieuPhanFailureTest`: tạo **thiếu section bắt buộc hoặc sai thứ tự.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `Foundation:BuilderContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `ExtensionTest`: thêm một biến thể hợp lệ của Foundation: Builder mà không sửa client/use case; nếu phải sửa, boundary chưa cô lập đúng change axis.
- Một test phản chứng cho phương án đơn giản hơn để chứng minh pattern mang giá trị, không chỉ tăng số type.
## Failure walkthrough

Khi **thiếu section bắt buộc hoặc sai thứ tự**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **report hoàn chỉnh mới được xuất**. Client test error theo semantics, không phụ thuộc message của SDK hoặc exception hạ tầng.

## Trade-off và phương án thay thế

Foundation: Builder làm change axis của **Tạo báo cáo nhiều phần** rõ hơn, đổi lại người học phải hiểu thêm contract, wiring và cách chọn implementation. Giá trị phải được chứng minh bằng việc thêm biến thể hoặc test failure mà client không đổi.

Hãy ưu tiên **constructor named arguments khi object đơn giản** nếu bài toán chỉ có một behavior ổn định, chưa có boundary ngoài hoặc test trực tiếp đã đủ rõ. Pattern không phải mục tiêu; mục tiêu là giữ invariant **report hoàn chỉnh mới được xuất.** với thiết kế dễ đọc nhất.
## Dấu hiệu lời giải chưa đạt

- Boundary của Builder không dùng ngôn ngữ **Tạo báo cáo nhiều phần**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **report hoàn chỉnh mới được xuất** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Thêm biến thể mới vẫn phải sửa logic trung tâm, chứng tỏ extension point đặt sai.

## Câu hỏi mở rộng

- Với **Tạo báo cáo nhiều phần**, metric nào chứng minh Builder giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Builder

Trong **Lời giải tham khảo — Builder (Foundation)** cấp Foundation, Builder phải làm rõ thứ tự bước, preset và validation liên bước; object hoàn tất nên immutable và không thể tồn tại ở trạng thái build dở.

### Test focus

Ở cấp **Foundation**, test incomplete build, preset và hai builder instance không rò state. Giữ test tại process boundary nhỏ nhất và ưu tiên semantics dễ đọc.

### Bằng chứng nên lưu

Với **Tạo báo cáo nhiều phần**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Builder. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
