# Lời giải tham khảo — Bridge (Foundation)

## Kết luận thiết kế

Lời giải chọn `Renderer` làm boundary vì nó bao quanh phần thay đổi của **Render báo cáo nhiều kênh** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **nội dung báo cáo độc lập định dạng render**, không phải chứng minh rằng mọi bài toán đều cần Bridge.

## Sơ đồ lời giải

```mermaid
classDiagram
    class Report {
      +handle(input)
    }
    class Renderer {
      <<interface>>
      +apply(input)
    }
    class HtmlRenderer
    class PdfRenderer
    Report --> Renderer : depends on
    Renderer <|.. HtmlRenderer
    Renderer <|.. PdfRenderer
```

## Các bước refactor

1. Viết test cho `Report` trước khi tách; test mô tả outcome chứ không khóa internal call.
2. Tách phần thay đổi thành `Renderer` với input/output cụ thể của **Render báo cáo nhiều kênh**.
3. Di chuyển một nhánh sang implementation đầu tiên; giữ validation dùng chung tại nơi ổn định.
4. Thay branch selection bằng dependency injection/composition root nhỏ.
5. Thêm biến thể **thêm MobileReport và JsonRenderer độc lập** và chạy cùng contract test.
6. So sánh với phương án đơn giản hơn: **kế thừa khi chỉ có một trục thay đổi**; ghi khi nào nên xóa abstraction.

## Phác thảo contract

```php
interface Renderer
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Render báo cáo nhiều kênh**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Bridge phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `RenderBaoCaoNhieuKenhBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **nội dung báo cáo độc lập định dạng render.** trên output/state, không assert tên concrete class.
- `RenderBaoCaoNhieuKenhFailureTest`: tạo **renderer thiếu capability hoặc encode lỗi.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `Foundation:BridgeContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `ExtensionTest`: thêm một biến thể hợp lệ của Foundation: Bridge mà không sửa client/use case; nếu phải sửa, boundary chưa cô lập đúng change axis.
- Một test phản chứng cho phương án đơn giản hơn để chứng minh pattern mang giá trị, không chỉ tăng số type.
## Failure walkthrough

Khi **renderer thiếu capability hoặc encode lỗi**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **nội dung báo cáo độc lập định dạng render**. Client test error theo semantics, không phụ thuộc message của SDK hoặc exception hạ tầng.

## Trade-off và phương án thay thế

Foundation: Bridge làm change axis của **Render báo cáo nhiều kênh** rõ hơn, đổi lại người học phải hiểu thêm contract, wiring và cách chọn implementation. Giá trị phải được chứng minh bằng việc thêm biến thể hoặc test failure mà client không đổi.

Hãy ưu tiên **kế thừa khi chỉ có một trục thay đổi** nếu bài toán chỉ có một behavior ổn định, chưa có boundary ngoài hoặc test trực tiếp đã đủ rõ. Pattern không phải mục tiêu; mục tiêu là giữ invariant **nội dung báo cáo độc lập định dạng render.** với thiết kế dễ đọc nhất.
## Dấu hiệu lời giải chưa đạt

- Boundary của Bridge không dùng ngôn ngữ **Render báo cáo nhiều kênh**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **nội dung báo cáo độc lập định dạng render** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Thêm biến thể mới vẫn phải sửa logic trung tâm, chứng tỏ extension point đặt sai.

## Câu hỏi mở rộng

- Với **Render báo cáo nhiều kênh**, metric nào chứng minh Bridge giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Bridge

Bài **Lời giải tham khảo — Bridge (Foundation)** cấp Foundation dùng Bridge khi abstraction và implementation thật sự thay đổi độc lập; hãy chứng minh tránh được tích Descartes bằng change scenario và contract test cho từng trục.

### Test focus

Ở cấp **Foundation**, test mọi cặp abstraction–implementation quan trọng và capability mismatch. Giữ test tại process boundary nhỏ nhất và ưu tiên semantics dễ đọc.

### Bằng chứng nên lưu

Với **Render báo cáo nhiều kênh**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Bridge. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
