# Decorator cấu hình đồ uống

## Câu chuyện nghiệp vụ

Giá và mô tả đồ uống thay đổi theo topping như sữa, caramel và kem; các tổ hợp tăng nhanh.

## Phiên bản ban đầu đang vướng gì?

`before.php` dùng subclass cho từng tổ hợp, dẫn đến bùng nổ class và khó ghép linh hoạt.

## Ý tưởng refactor

`after.php` bọc `Beverage` bằng decorator; mỗi wrapper cộng giá và mô tả rồi ủy quyền.

## Cách đọc ví dụ

1. Đọc câu chuyện **Decorator cấu hình đồ uống** và viết lại invariant nghiệp vụ bằng một câu; đừng bắt đầu từ tên pattern.
2. Chạy `before.php`, đối chiếu output với pain point: `before.php` dùng subclass cho từng tổ hợp, dẫn đến bùng nổ class và khó ghép linh hoạt.
3. Vẽ dependency/flow hiện tại và đánh dấu nơi thay đổi hoặc failure lan sang client.
4. Chạy `after.php`, kiểm tra trọng tâm: Decorator giữ cùng contract với object được bọc.
5. Mô phỏng tình huống phản chứng: Thứ tự wrapper có thể ảnh hưởng kết quả, đặc biệt với retry/cache/security. Sau đó giải thích vì sao refactor giảm blast radius và chi phí abstraction nào được thêm vào.

## Điều cần quan sát riêng của bài

- Decorator giữ cùng contract với object được bọc.
- Thứ tự wrapper có thể ảnh hưởng kết quả, đặc biệt với retry/cache/security.
- Nhiều decorator lồng nhau cần naming và test để vẫn dễ debug.

## Thực hành mở rộng

1. Thêm topping giới hạn số lượng.
2. Kiểm tra thứ tự giảm giá trước hay sau thuế.
3. So sánh decorator runtime với subclass cố định.

## Khi giải pháp trước vẫn hợp lý

Một cấu trúc dữ liệu topping và hàm tính tổng có thể đơn giản hơn nếu topping chỉ là dữ liệu.

## Cách chạy

```bash
php before.php
php after.php
```

## Tài liệu liên quan

- [04 Decorator](../../../docs/02-structural/04-decorator.md)
- [Pattern Comparison](../../../cheatsheets/pattern-comparison.md)

## Tệp trong ví dụ

- [`before.php`](before.php): hiện thực baseline của **Decorator cấu hình đồ uống**; dùng file này để tái hiện vấn đề “`before.php` dùng subclass cho từng tổ hợp, dẫn đến bùng nổ class và khó ghép linh hoạt.”.
- [`after.php`](after.php): hiện thực hướng refactor “`after.php` bọc `Beverage` bằng decorator; mỗi wrapper cộng giá và mô tả rồi ủy quyền.”; so sánh bằng output, invariant và failure behavior.
- `test.php` (nếu có): chạy contract/failure scenario được nêu trong “Điều cần quan sát”; test không nên chỉ assert concrete class được gọi.
