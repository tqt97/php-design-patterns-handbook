# Observer cho sự kiện đơn hàng

## Câu chuyện nghiệp vụ

Khi đơn được xác nhận, hệ thống cần gửi email, ghi audit và cập nhật analytics. Các side effect thay đổi độc lập với use case tạo đơn.

## Phiên bản ban đầu đang vướng gì?

`before.php` gọi trực tiếp từng service sau khi lưu đơn. Order service phải biết mọi subscriber và rất khó thêm/bỏ side effect.

## Ý tưởng refactor

`after.php` phát `OrderConfirmed`; các listener đăng ký độc lập và nhận dữ liệu sự kiện bất biến.

## Cách đọc ví dụ

1. Đọc câu chuyện **Observer cho sự kiện đơn hàng** và viết lại invariant nghiệp vụ bằng một câu; đừng bắt đầu từ tên pattern.
2. Chạy `before.php`, đối chiếu output với pain point: `before.php` gọi trực tiếp từng service sau khi lưu đơn. Order service phải biết mọi subscriber và rất khó thêm/bỏ side effect.
3. Vẽ dependency/flow hiện tại và đánh dấu nơi thay đổi hoặc failure lan sang client.
4. Chạy `after.php`, kiểm tra trọng tâm: Tên event phải mô tả điều đã xảy ra, không phải command mơ hồ.
5. Mô phỏng tình huống phản chứng: Listener đồng bộ kéo dài transaction; listener async cần idempotency và retry policy. Sau đó giải thích vì sao refactor giảm blast radius và chi phí abstraction nào được thêm vào.

## Điều cần quan sát riêng của bài

- Tên event phải mô tả điều đã xảy ra, không phải command mơ hồ.
- Listener đồng bộ kéo dài transaction; listener async cần idempotency và retry policy.
- Phải quyết định rõ lỗi một listener có làm thất bại toàn bộ dispatch hay không.

## Thực hành mở rộng

1. Thêm listener tích điểm nhưng bảo đảm không cộng hai lần.
2. Mô phỏng email lỗi và chọn chính sách fail-fast hoặc continue.
3. Tách integration event khỏi domain event khi gửi sang hệ thống khác.

## Khi giải pháp trước vẫn hợp lý

Gọi trực tiếp rõ hơn khi chỉ có một side effect bắt buộc trong cùng transaction.

## Cách chạy

```bash
php before.php
php after.php
```

## Tài liệu liên quan

- [07 Observer](../../../docs/03-behavioral/07-observer.md)
- [03 Outbox Inbox](../../../handbook/microservices/04-outbox-inbox.md)

## Tệp trong ví dụ

- [`before.php`](before.php): hiện thực baseline của **Observer cho sự kiện đơn hàng**; dùng file này để tái hiện vấn đề “`before.php` gọi trực tiếp từng service sau khi lưu đơn. Order service phải biết mọi subscriber và rất khó thêm/bỏ side effect.”.
- [`after.php`](after.php): hiện thực hướng refactor “`after.php` phát `OrderConfirmed`; các listener đăng ký độc lập và nhận dữ liệu sự kiện bất biến.”; so sánh bằng output, invariant và failure behavior.
- `test.php` (nếu có): chạy contract/failure scenario được nêu trong “Điều cần quan sát”; test không nên chỉ assert concrete class được gọi.
