# Lời giải tham khảo — Template Method (Foundation)

## Kết luận thiết kế

Lời giải chọn `ImportTemplate` làm boundary vì nó bao quanh phần thay đổi của **Quy trình import nhiều định dạng** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **thứ tự parse-validate-persist cố định**, không phải chứng minh rằng mọi bài toán đều cần Template Method.

## Sơ đồ lời giải

```mermaid
classDiagram
    class ImportJob {
      +handle(input)
    }
    class ImportTemplate {
      <<interface>>
      +apply(input)
    }
    class CsvImportTemplate
    class JsonImportTemplate
    ImportJob --> ImportTemplate : depends on
    ImportTemplate <|.. CsvImportTemplate
    ImportTemplate <|.. JsonImportTemplate
```

## Các bước refactor

1. Viết test cho `ImportJob` trước khi tách; test mô tả outcome chứ không khóa internal call.
2. Tách phần thay đổi thành `ImportTemplate` với input/output cụ thể của **Quy trình import nhiều định dạng**.
3. Di chuyển một nhánh sang implementation đầu tiên; giữ validation dùng chung tại nơi ổn định.
4. Thay branch selection bằng dependency injection/composition root nhỏ.
5. Thêm biến thể **thêm XML importer override hook hợp lệ** và chạy cùng contract test.
6. So sánh với phương án đơn giản hơn: **composition khi các bước cần hoán đổi tự do**; ghi khi nào nên xóa abstraction.

## Phác thảo contract

```php
interface ImportTemplate
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Quy trình import nhiều định dạng**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Template Method phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `QuyTrinhImportNhieuInhDangBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **thứ tự parse-validate-persist cố định.** trên output/state, không assert tên concrete class.
- `QuyTrinhImportNhieuInhDangFailureTest`: tạo **subclass bỏ qua bước bắt buộc.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `Foundation:TemplateMethodContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `ExtensionTest`: thêm một biến thể hợp lệ của Foundation: Template Method mà không sửa client/use case; nếu phải sửa, boundary chưa cô lập đúng change axis.
- Một test phản chứng cho phương án đơn giản hơn để chứng minh pattern mang giá trị, không chỉ tăng số type.
## Failure walkthrough

Khi **subclass bỏ qua bước bắt buộc**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **thứ tự parse-validate-persist cố định**. Client test error theo semantics, không phụ thuộc message của SDK hoặc exception hạ tầng.

## Trade-off và phương án thay thế

Foundation: Template Method làm change axis của **Quy trình import nhiều định dạng** rõ hơn, đổi lại người học phải hiểu thêm contract, wiring và cách chọn implementation. Giá trị phải được chứng minh bằng việc thêm biến thể hoặc test failure mà client không đổi.

Hãy ưu tiên **composition khi các bước cần hoán đổi tự do** nếu bài toán chỉ có một behavior ổn định, chưa có boundary ngoài hoặc test trực tiếp đã đủ rõ. Pattern không phải mục tiêu; mục tiêu là giữ invariant **thứ tự parse-validate-persist cố định.** với thiết kế dễ đọc nhất.
## Dấu hiệu lời giải chưa đạt

- Boundary của Template Method không dùng ngôn ngữ **Quy trình import nhiều định dạng**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **thứ tự parse-validate-persist cố định** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Thêm biến thể mới vẫn phải sửa logic trung tâm, chứng tỏ extension point đặt sai.

## Câu hỏi mở rộng

- Với **Quy trình import nhiều định dạng**, metric nào chứng minh Template Method giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Template Method

Ở **Lời giải tham khảo — Template Method (Foundation)** cấp Foundation, Template Method chỉ bảo vệ thứ tự bất biến; hook phải nhỏ, có contract và nếu bước cần thay runtime thì composition/Strategy phù hợp hơn inheritance.

### Test focus

Ở cấp **Foundation**, test invariant của skeleton và từng hook mà không subclass phá transaction. Giữ test tại process boundary nhỏ nhất và ưu tiên semantics dễ đọc.

### Bằng chứng nên lưu

Với **Quy trình import nhiều định dạng**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Template Method. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
