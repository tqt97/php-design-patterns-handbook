# Lời giải tham khảo — Unit of Work (Foundation)

## Kết luận thiết kế

Lời giải chọn `UnitOfWork` làm boundary vì nó bao quanh phần thay đổi của **Lưu nhiều thay đổi domain** nhưng không kéo validation, transaction hoặc orchestration ổn định vào abstraction. Mục tiêu là bảo vệ **commit tất cả hoặc rollback tất cả**, không phải chứng minh rằng mọi bài toán đều cần Unit of Work.

## Sơ đồ lời giải

```mermaid
classDiagram
    class CheckoutApplication {
      +handle(input)
    }
    class UnitOfWork {
      <<interface>>
      +apply(input)
    }
    class OrderUnitOfWork
    class PaymentUnitOfWork
    CheckoutApplication --> UnitOfWork : depends on
    UnitOfWork <|.. OrderUnitOfWork
    UnitOfWork <|.. PaymentUnitOfWork
```

## Các bước refactor

1. Viết test cho `CheckoutApplication` trước khi tách; test mô tả outcome chứ không khóa internal call.
2. Tách phần thay đổi thành `UnitOfWork` với input/output cụ thể của **Lưu nhiều thay đổi domain**.
3. Di chuyển một nhánh sang implementation đầu tiên; giữ validation dùng chung tại nơi ổn định.
4. Thay branch selection bằng dependency injection/composition root nhỏ.
5. Thêm biến thể **thêm after-commit event collection** và chạy cùng contract test.
6. So sánh với phương án đơn giản hơn: **transaction script trực tiếp khi scope nhỏ**; ghi khi nào nên xóa abstraction.

## Phác thảo contract

```php
interface UnitOfWork
{
    /** Trả về kết quả domain hoặc ném lỗi application có nghĩa. */
    public function apply(array $input): mixed;
}
```

Trong implementation hoàn chỉnh của **Lưu nhiều thay đổi domain**, thay `array`/`mixed` bằng input và result có tên theo domain. Contract của Unit of Work phải làm rõ precondition, postcondition và lỗi có thể quan sát; đoạn trên chỉ minh họa hướng dependency.

## Test suite tối thiểu

- `LuuNhieuThayOiDomainBehaviorTest`: dựng fixture nhỏ nhất và assert trực tiếp invariant **commit tất cả hoặc rollback tất cả.** trên output/state, không assert tên concrete class.
- `LuuNhieuThayOiDomainFailureTest`: tạo **partial commit giữa order và inventory.**, kiểm tra error taxonomy và xác nhận state/side effect không ở trạng thái nửa vời.
- `Foundation:UnitofWorkContractTest`: chạy cùng bộ contract cho mọi implementation hoặc variant được bài yêu cầu.
- `ExtensionTest`: thêm một biến thể hợp lệ của Foundation: Unit of Work mà không sửa client/use case; nếu phải sửa, boundary chưa cô lập đúng change axis.
- Một test phản chứng cho phương án đơn giản hơn để chứng minh pattern mang giá trị, không chỉ tăng số type.
## Failure walkthrough

Khi **partial commit giữa order và inventory**, implementation không được trả `null`/`false` mơ hồ. Nó phải tạo error có taxonomy rõ, giữ correlation/operation data cần thiết và không phá invariant **commit tất cả hoặc rollback tất cả**. Client test error theo semantics, không phụ thuộc message của SDK hoặc exception hạ tầng.

## Trade-off và phương án thay thế

Foundation: Unit of Work làm change axis của **Lưu nhiều thay đổi domain** rõ hơn, đổi lại người học phải hiểu thêm contract, wiring và cách chọn implementation. Giá trị phải được chứng minh bằng việc thêm biến thể hoặc test failure mà client không đổi.

Hãy ưu tiên **transaction script trực tiếp khi scope nhỏ** nếu bài toán chỉ có một behavior ổn định, chưa có boundary ngoài hoặc test trực tiếp đã đủ rõ. Pattern không phải mục tiêu; mục tiêu là giữ invariant **commit tất cả hoặc rollback tất cả.** với thiết kế dễ đọc nhất.
## Dấu hiệu lời giải chưa đạt

- Boundary của Unit of Work không dùng ngôn ngữ **Lưu nhiều thay đổi domain**, khiến người đọc vẫn phải biết concrete detail.
- Test không chứng minh invariant **commit tất cả hoặc rollback tất cả** hoặc chỉ xác nhận method được gọi.
- Selection, transaction hay error mapping vẫn nằm rải rác ở client nên blast radius không giảm.
- Diagram không khớp dependency thực trong code hoặc bỏ qua failure path quan trọng.
- Thêm biến thể mới vẫn phải sửa logic trung tâm, chứng tỏ extension point đặt sai.

## Câu hỏi mở rộng

- Với **Lưu nhiều thay đổi domain**, metric nào chứng minh Unit of Work giảm rủi ro thay đổi thay vì chỉ tăng số type?
- Khi số biến thể tăng, ai sở hữu selection, versioning và compatibility của boundary?
- Failure nào buộc thiết kế phải đổi, và failure nào nên được xử lý bên ngoài pattern?
- Điều kiện cleanup nào cho phép hợp nhất implementation hoặc xóa abstraction này?

## Ghi chú triển khai chuyên biệt cho Unit of Work

Trong **Lời giải tham khảo — Unit of Work (Foundation)** cấp Foundation, Unit of Work gom thay đổi và commit atomically; network call nằm ngoài DB transaction và cần outbox/compensation nếu phải phối hợp side effect.

### Test focus

Ở cấp **Foundation**, test commit, rollback, nested behavior, deadlock retry và after-commit event. Giữ test tại process boundary nhỏ nhất và ưu tiên semantics dễ đọc.

### Bằng chứng nên lưu

Với **Lưu nhiều thay đổi domain**, lưu sơ đồ dependency trước/sau, fixture tái hiện lỗi, test chứng minh invariant, commit chuyển đổi và ADR của Unit of Work. Reviewer cần nhìn được bằng chứng rằng boundary mới giảm blast radius hoặc làm failure dễ kiểm soát hơn, không chỉ thấy nhiều class hơn.
