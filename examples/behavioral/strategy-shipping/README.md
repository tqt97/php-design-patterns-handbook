# Strategy tính phí vận chuyển

## Câu chuyện nghiệp vụ

Checkout phải tính phí cho giao hàng tiêu chuẩn, giao nhanh và giao trong ngày. Mỗi chính sách dùng vùng giao, khối lượng và ngưỡng miễn phí khác nhau.

## Phiên bản ban đầu đang vướng gì?

`before.php` đặt toàn bộ công thức trong một `switch`. Mỗi carrier mới buộc sửa checkout, còn test phải đi qua nhiều nhánh không liên quan.

## Ý tưởng refactor

`after.php` đưa công thức vào các `ShippingFeePolicy`. Checkout nhận policy đã được chọn từ composition root và chỉ gọi một contract ổn định.

## Cách đọc ví dụ

1. Đọc câu chuyện **Strategy tính phí vận chuyển** và viết lại invariant nghiệp vụ bằng một câu; đừng bắt đầu từ tên pattern.
2. Chạy `before.php`, đối chiếu output với pain point: `before.php` đặt toàn bộ công thức trong một `switch`. Mỗi carrier mới buộc sửa checkout, còn test phải đi qua nhiều nhánh không liên quan.
3. Vẽ dependency/flow hiện tại và đánh dấu nơi thay đổi hoặc failure lan sang client.
4. Chạy `after.php`, kiểm tra trọng tâm: Policy không tự chọn chính nó; việc chọn policy là trách nhiệm wiring hoặc resolver.
5. Mô phỏng tình huống phản chứng: Mọi policy phải dùng cùng đơn vị tiền, cùng cách làm tròn và không trả số âm. Sau đó giải thích vì sao refactor giảm blast radius và chi phí abstraction nào được thêm vào.

## Điều cần quan sát riêng của bài

- Policy không tự chọn chính nó; việc chọn policy là trách nhiệm wiring hoặc resolver.
- Mọi policy phải dùng cùng đơn vị tiền, cùng cách làm tròn và không trả số âm.
- Contract test nên chạy cho tất cả policy để ngăn semantics bị lệch.

## Thực hành mở rộng

1. Thêm chính sách miễn phí cho đơn thành viên nhưng không sửa checkout.
2. Thêm `ShippingQuote` chứa phí, ETA và lý do phụ phí thay vì trả một số nguyên.
3. Viết test chứng minh policy giao trong ngày từ chối khu vực không hỗ trợ.

## Khi giải pháp trước vẫn hợp lý

Giữ `match` trực tiếp nếu chỉ có hai công thức nhỏ, ổn định và không cần thay độc lập.

## Cách chạy

```bash
php before.php
php after.php
```

## Tài liệu liên quan

- [09 Strategy](../../../docs/03-behavioral/09-strategy.md)
- [Pattern Comparison](../../../cheatsheets/pattern-comparison.md)

## Tệp trong ví dụ

- [`before.php`](before.php): hiện thực baseline của **Strategy tính phí vận chuyển**; dùng file này để tái hiện vấn đề “`before.php` đặt toàn bộ công thức trong một `switch`. Mỗi carrier mới buộc sửa checkout, còn test phải đi qua nhiều nhánh không liên quan.”.
- [`after.php`](after.php): hiện thực hướng refactor “`after.php` đưa công thức vào các `ShippingFeePolicy`. Checkout nhận policy đã được chọn từ composition root và chỉ gọi một contract ổn định.”; so sánh bằng output, invariant và failure behavior.
- `test.php` (nếu có): chạy contract/failure scenario được nêu trong “Điều cần quan sát”; test không nên chỉ assert concrete class được gọi.
