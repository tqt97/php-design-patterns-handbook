# Lời giải tham khảo — Facade (Foundation)

## Kết luận thiết kế

Lời giải chọn `VideoPipelineFacade` làm boundary vì nó bao quanh phần thay đổi của **Xử lý video upload** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **upload chỉ complete khi encode và persist thành công**, không phải chứng minh rằng mọi bài toán đều cần Facade.

## Sơ đồ lời giải

```mermaid
classDiagram
    class VideoFacade {
      +handle(input)
    }
    class VideoPipelineFacade {
      <<interface>>
      +apply(input)
    }
    class VideoTranscoder
    class ThumbnailGenerator
    VideoFacade --> VideoPipelineFacade : depends on
    VideoPipelineFacade <|.. VideoTranscoder
    VideoPipelineFacade <|.. ThumbnailGenerator
```

## Các bước refactor

1. Viết test cho `VideoFacade` trước khi tách; test mô tả outcome chứ không khóa internal call.
2. Tách phần thay đổi thành `VideoPipelineFacade` với input/output cụ thể của **Xử lý video upload**.
3. Di chuyển một nhánh sang implementation đầu tiên; giữ validation dùng chung tại nơi ổn định.
4. Thay branch selection bằng dependency injection/composition root nhỏ.
5. Thêm biến thể **thêm thumbnail subsystem nhưng client không đổi** và chạy cùng contract test.
6. So sánh với phương án đơn giản hơn: **gọi trực tiếp khi workflow chỉ có một bước**; ghi khi nào nên xóa abstraction.

## Phác thảo contract

```php
interface VideoPipelineFacade
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Xử lý video upload**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Facade phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `XuLyVideoUploadBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **upload chỉ complete khi encode và persist thành công.** trên output/state, không assert tên concrete class.
- `XuLyVideoUploadFailureTest`: tạo **một subsystem fail giữa workflow.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `Foundation:FacadeContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `ExtensionTest`: thêm một biến thể hợp lệ của Foundation: Facade mà không sửa client/use case; nếu phải sửa, boundary chưa cô lập đúng change axis.
- Một test phản chứng cho phương án đơn giản hơn để chứng minh pattern mang giá trị, không chỉ tăng số type.
## Failure walkthrough

Khi **một subsystem fail giữa workflow**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **upload chỉ complete khi encode và persist thành công**. Client test error theo semantics, không phụ thuộc message của SDK hoặc exception hạ tầng.

## Trade-off và phương án thay thế

Foundation: Facade làm change axis của **Xử lý video upload** rõ hơn, đổi lại người học phải hiểu thêm contract, wiring và cách chọn implementation. Giá trị phải được chứng minh bằng việc thêm biến thể hoặc test failure mà client không đổi.

Hãy ưu tiên **gọi trực tiếp khi workflow chỉ có một bước** nếu bài toán chỉ có một behavior ổn định, chưa có boundary ngoài hoặc test trực tiếp đã đủ rõ. Pattern không phải mục tiêu; mục tiêu là giữ invariant **upload chỉ complete khi encode và persist thành công.** với thiết kế dễ đọc nhất.
## Dấu hiệu lời giải chưa đạt

- Boundary của Facade không dùng ngôn ngữ **Xử lý video upload**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **upload chỉ complete khi encode và persist thành công** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Thêm biến thể mới vẫn phải sửa logic trung tâm, chứng tỏ extension point đặt sai.

## Câu hỏi mở rộng

- Với **Xử lý video upload**, metric nào chứng minh Facade giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Facade

Ở **Lời giải tham khảo — Facade (Foundation)** cấp Foundation, Facade cung cấp entry point có mục tiêu rõ cho subsystem; nó không được che giấu state machine, transaction boundary hoặc failure semantics khiến caller không thể recovery.

### Test focus

Ở cấp **Foundation**, test orchestration order, partial failure, compensation và idempotent resume. Giữ test tại process boundary nhỏ nhất và ưu tiên semantics dễ đọc.

### Bằng chứng nên lưu

Với **Xử lý video upload**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Facade. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
