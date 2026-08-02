# Adapter cho API thời tiết bên ngoài

## Câu chuyện nghiệp vụ

Domain cần nhiệt độ theo Celsius và mã trạng thái chuẩn; SDK cũ trả Fahrenheit, key khác và exception vendor.

## Phiên bản ban đầu đang vướng gì?

`before.php` để application service gọi SDK và tự chuyển đổi dữ liệu, khiến vendor contract lan rộng.

## Ý tưởng refactor

`after.php` cung cấp `WeatherService` nội bộ; adapter chuyển unit, field và lỗi vendor tại boundary.

## Cách đọc ví dụ

1. Đọc câu chuyện **Adapter cho API thời tiết bên ngoài** và viết lại invariant nghiệp vụ bằng một câu; đừng bắt đầu từ tên pattern.
2. Chạy `before.php`, đối chiếu output với pain point: `before.php` để application service gọi SDK và tự chuyển đổi dữ liệu, khiến vendor contract lan rộng.
3. Vẽ dependency/flow hiện tại và đánh dấu nơi thay đổi hoặc failure lan sang client.
4. Chạy `after.php`, kiểm tra trọng tâm: Adapter dịch contract và ngôn ngữ lỗi, không chứa business rule dự báo.
5. Mô phỏng tình huống phản chứng: Mapping phải được contract-test bằng fixture đại diện từ vendor. Sau đó giải thích vì sao refactor giảm blast radius và chi phí abstraction nào được thêm vào.

## Điều cần quan sát riêng của bài

- Adapter dịch contract và ngôn ngữ lỗi, không chứa business rule dự báo.
- Mapping phải được contract-test bằng fixture đại diện từ vendor.
- Không để DTO hoặc exception của SDK thoát qua port nội bộ.

## Thực hành mở rộng

1. Thêm adapter cho nhà cung cấp thứ hai.
2. Map timeout vendor thành `WeatherUnavailable`.
3. Xử lý giá trị sentinel hoặc field thiếu bằng validation rõ.

## Khi giải pháp trước vẫn hợp lý

Gọi SDK trực tiếp chấp nhận được trong script ngắn, không có domain boundary và không cần thay vendor.

## Cách chạy

```bash
php before.php
php after.php
```

## Tài liệu liên quan

- [01 Adapter](../../../docs/02-structural/01-adapter.md)
- [02 Ports And Adapters](../../../handbook/clean-architecture/04-ports-adapters.md)

## Tệp trong ví dụ

- [`before.php`](before.php): hiện thực baseline của **Adapter cho API thời tiết bên ngoài**; dùng file này để tái hiện vấn đề “`before.php` để application service gọi SDK và tự chuyển đổi dữ liệu, khiến vendor contract lan rộng.”.
- [`after.php`](after.php): hiện thực hướng refactor “`after.php` cung cấp `WeatherService` nội bộ; adapter chuyển unit, field và lỗi vendor tại boundary.”; so sánh bằng output, invariant và failure behavior.
- `test.php` (nếu có): chạy contract/failure scenario được nêu trong “Điều cần quan sát”; test không nên chỉ assert concrete class được gọi.
