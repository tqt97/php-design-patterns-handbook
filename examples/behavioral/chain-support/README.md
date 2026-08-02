# Chain of Responsibility cho phân loại ticket

## Câu chuyện nghiệp vụ

Ticket hỗ trợ cần qua kiểm tra quyền, phát hiện sự cố khẩn cấp, phân loại billing và chuyển nhóm kỹ thuật.

## Phiên bản ban đầu đang vướng gì?

`before.php` dùng chuỗi `if/elseif`; thứ tự xử lý bị dính vào một method dài và khó tái cấu hình.

## Ý tưởng refactor

`after.php` tạo các handler có thể xử lý hoặc chuyển tiếp. Composition root thể hiện thứ tự chain.

## Cách đọc ví dụ

1. Đọc câu chuyện **Chain of Responsibility cho phân loại ticket** và viết lại invariant nghiệp vụ bằng một câu; đừng bắt đầu từ tên pattern.
2. Chạy `before.php`, đối chiếu output với pain point: `before.php` dùng chuỗi `if/elseif`; thứ tự xử lý bị dính vào một method dài và khó tái cấu hình.
3. Vẽ dependency/flow hiện tại và đánh dấu nơi thay đổi hoặc failure lan sang client.
4. Chạy `after.php`, kiểm tra trọng tâm: Mỗi handler chỉ biết điều kiện của mình và next handler.
5. Mô phỏng tình huống phản chứng: Thứ tự chain là behavior, phải được test như một phần của cấu hình. Sau đó giải thích vì sao refactor giảm blast radius và chi phí abstraction nào được thêm vào.

## Điều cần quan sát riêng của bài

- Mỗi handler chỉ biết điều kiện của mình và next handler.
- Thứ tự chain là behavior, phải được test như một phần của cấu hình.
- Cần đường xử lý rõ khi không handler nào nhận ticket.

## Thực hành mở rộng

1. Thêm handler VIP đứng trước billing.
2. Ghi trace handler nào đã bỏ qua hoặc nhận ticket.
3. Tạo test phát hiện cấu hình chain thiếu fallback.

## Khi giải pháp trước vẫn hợp lý

Một danh sách điều kiện nhỏ, cố định và chỉ dùng một nơi có thể dễ đọc hơn chain.

## Cách chạy

```bash
php before.php
php after.php
```

## Tài liệu liên quan

- [01 Chain Of Responsibility](../../../docs/03-behavioral/01-chain-of-responsibility.md)
- [Pattern Comparison](../../../cheatsheets/pattern-comparison.md)

## Tệp trong ví dụ

- [`before.php`](before.php): hiện thực baseline của **Chain of Responsibility cho phân loại ticket**; dùng file này để tái hiện vấn đề “`before.php` dùng chuỗi `if/elseif`; thứ tự xử lý bị dính vào một method dài và khó tái cấu hình.”.
- [`after.php`](after.php): hiện thực hướng refactor “`after.php` tạo các handler có thể xử lý hoặc chuyển tiếp. Composition root thể hiện thứ tự chain.”; so sánh bằng output, invariant và failure behavior.
- `test.php` (nếu có): chạy contract/failure scenario được nêu trong “Điều cần quan sát”; test không nên chỉ assert concrete class được gọi.
