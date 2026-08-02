# Builder tạo báo cáo cấu hình phức tạp

## Câu chuyện nghiệp vụ

Báo cáo có khoảng thời gian, bộ lọc, nhóm cột, định dạng và tùy chọn hiển thị. Không phải tổ hợp nào cũng hợp lệ.

## Phiên bản ban đầu đang vướng gì?

`before.php` dùng constructor dài với nhiều tham số nullable và boolean khó hiểu.

## Ý tưởng refactor

`after.php` dùng builder có method theo ngôn ngữ báo cáo và validate invariant tại `build()`.

## Cách đọc ví dụ

1. Đọc câu chuyện **Builder tạo báo cáo cấu hình phức tạp** và viết lại invariant nghiệp vụ bằng một câu; đừng bắt đầu từ tên pattern.
2. Chạy `before.php`, đối chiếu output với pain point: `before.php` dùng constructor dài với nhiều tham số nullable và boolean khó hiểu.
3. Vẽ dependency/flow hiện tại và đánh dấu nơi thay đổi hoặc failure lan sang client.
4. Chạy `after.php`, kiểm tra trọng tâm: Builder hữu ích khi quá trình tạo có nhiều bước hoặc invariant liên thuộc.
5. Mô phỏng tình huống phản chứng: Object sau `build()` nên hợp lệ và ưu tiên bất biến. Sau đó giải thích vì sao refactor giảm blast radius và chi phí abstraction nào được thêm vào.

## Điều cần quan sát riêng của bài

- Builder hữu ích khi quá trình tạo có nhiều bước hoặc invariant liên thuộc.
- Object sau `build()` nên hợp lệ và ưu tiên bất biến.
- Builder không nên che giấu default quan trọng hoặc cho phép trạng thái dở dang rò ra.

## Thực hành mở rộng

1. Thêm preset báo cáo doanh thu tháng.
2. Bắt lỗi khi group theo cột không nằm trong projection.
3. Tạo director chỉ khi có nhiều sequence build được tái sử dụng.

## Khi giải pháp trước vẫn hợp lý

Named constructor hoặc constructor ngắn rõ hơn khi object chỉ có vài trường bắt buộc.

## Cách chạy

```bash
php before.php
php after.php
```

## Tài liệu liên quan

- [04 Builder](../../../docs/01-creational/03-builder.md)
- [Pattern Comparison](../../../cheatsheets/pattern-comparison.md)

## Tệp trong ví dụ

- [`before.php`](before.php): hiện thực baseline của **Builder tạo báo cáo cấu hình phức tạp**; dùng file này để tái hiện vấn đề “`before.php` dùng constructor dài với nhiều tham số nullable và boolean khó hiểu.”.
- [`after.php`](after.php): hiện thực hướng refactor “`after.php` dùng builder có method theo ngôn ngữ báo cáo và validate invariant tại `build()`.”; so sánh bằng output, invariant và failure behavior.
- `test.php` (nếu có): chạy contract/failure scenario được nêu trong “Điều cần quan sát”; test không nên chỉ assert concrete class được gọi.
