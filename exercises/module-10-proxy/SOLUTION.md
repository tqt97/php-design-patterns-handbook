# Lời giải tham khảo — Proxy (Foundation)

## Kết luận thiết kế

Lời giải chọn `DocumentReader` làm boundary vì nó bao quanh phần thay đổi của **Bảo vệ tài liệu theo quyền** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **chỉ principal hợp lệ đọc được tài liệu**, không phải chứng minh rằng mọi bài toán đều cần Proxy.

## Sơ đồ lời giải

```mermaid
classDiagram
    class DocumentService {
      +handle(input)
    }
    class DocumentReader {
      <<interface>>
      +apply(input)
    }
    class AuthorizedDocumentReader
    class CachedDocumentReader
    DocumentService --> DocumentReader : depends on
    DocumentReader <|.. AuthorizedDocumentReader
    DocumentReader <|.. CachedDocumentReader
```

## Các bước refactor

1. Viết test cho `DocumentService` trước khi tách; test mô tả outcome chứ không khóa internal call.
2. Tách phần thay đổi thành `DocumentReader` với input/output cụ thể của **Bảo vệ tài liệu theo quyền**.
3. Di chuyển một nhánh sang implementation đầu tiên; giữ validation dùng chung tại nơi ổn định.
4. Thay branch selection bằng dependency injection/composition root nhỏ.
5. Thêm biến thể **thêm audit proxy** và chạy cùng contract test.
6. So sánh với phương án đơn giản hơn: **check quyền trực tiếp khi chỉ một call site**; ghi khi nào nên xóa abstraction.

## Phác thảo contract

```php
interface DocumentReader
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Bảo vệ tài liệu theo quyền**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Proxy phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `BaoVeTaiLieuTheoQuyenBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **chỉ principal hợp lệ đọc được tài liệu.** trên output/state, không assert tên concrete class.
- `BaoVeTaiLieuTheoQuyenFailureTest`: tạo **proxy cache nhầm tenant hoặc bypass authorization.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `Foundation:ProxyContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `ExtensionTest`: thêm một biến thể hợp lệ của Foundation: Proxy mà không sửa client/use case; nếu phải sửa, boundary chưa cô lập đúng change axis.
- Một test phản chứng cho phương án đơn giản hơn để chứng minh pattern mang giá trị, không chỉ tăng số type.
## Failure walkthrough

Khi **proxy cache nhầm tenant hoặc bypass authorization**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **chỉ principal hợp lệ đọc được tài liệu**. Client test error theo semantics, không phụ thuộc message của SDK hoặc exception hạ tầng.

## Trade-off và phương án thay thế

Foundation: Proxy làm change axis của **Bảo vệ tài liệu theo quyền** rõ hơn, đổi lại người học phải hiểu thêm contract, wiring và cách chọn implementation. Giá trị phải được chứng minh bằng việc thêm biến thể hoặc test failure mà client không đổi.

Hãy ưu tiên **check quyền trực tiếp khi chỉ một call site** nếu bài toán chỉ có một behavior ổn định, chưa có boundary ngoài hoặc test trực tiếp đã đủ rõ. Pattern không phải mục tiêu; mục tiêu là giữ invariant **chỉ principal hợp lệ đọc được tài liệu.** với thiết kế dễ đọc nhất.
## Dấu hiệu lời giải chưa đạt

- Boundary của Proxy không dùng ngôn ngữ **Bảo vệ tài liệu theo quyền**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **chỉ principal hợp lệ đọc được tài liệu** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Thêm biến thể mới vẫn phải sửa logic trung tâm, chứng tỏ extension point đặt sai.

## Câu hỏi mở rộng

- Với **Bảo vệ tài liệu theo quyền**, metric nào chứng minh Proxy giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Proxy

Bài **Lời giải tham khảo — Proxy (Foundation)** cấp Foundation dùng Proxy để kiểm soát authorization/remote/lazy/cache access; cache key và authorization decision phải bao gồm tenant/user/security scope để tránh data leak.

### Test focus

Ở cấp **Foundation**, test denied access không gọi subject, cache isolation và stale authorization. Giữ test tại process boundary nhỏ nhất và ưu tiên semantics dễ đọc.

### Bằng chứng nên lưu

Với **Bảo vệ tài liệu theo quyền**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Proxy. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
