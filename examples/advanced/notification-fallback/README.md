# Notification fallback đa kênh

## Câu chuyện nghiệp vụ

Thông báo ưu tiên push, sau đó SMS, cuối cùng email; từng kênh có lỗi tạm thời và lỗi vĩnh viễn khác nhau.

## Phiên bản ban đầu đang vướng gì?

`before.php` dùng try/catch lồng nhau và lặp logic retry/fallback.

## Ý tưởng refactor

`after.php` tách channel strategy, policy phân loại lỗi và coordinator fallback.

## Cách đọc ví dụ

1. Đọc câu chuyện **Notification fallback đa kênh** và viết lại invariant nghiệp vụ bằng một câu; đừng bắt đầu từ tên pattern.
2. Chạy `before.php`, đối chiếu output với pain point: `before.php` dùng try/catch lồng nhau và lặp logic retry/fallback.
3. Vẽ dependency/flow hiện tại và đánh dấu nơi thay đổi hoặc failure lan sang client.
4. Chạy `after.php`, kiểm tra trọng tâm: Fallback chỉ chạy với lỗi được phép; validation error không nên thử kênh khác.
5. Mô phỏng tình huống phản chứng: Mỗi attempt cần correlation id và kết quả quan sát được. Sau đó giải thích vì sao refactor giảm blast radius và chi phí abstraction nào được thêm vào.

## Điều cần quan sát riêng của bài

- Fallback chỉ chạy với lỗi được phép; validation error không nên thử kênh khác.
- Mỗi attempt cần correlation id và kết quả quan sát được.
- Retry và fallback là hai quyết định khác nhau.

## Thực hành mở rộng

1. Thêm quiet hours cho SMS.
2. Bảo đảm một notification không gửi trùng khi retry coordinator.
3. Ghi audit chuỗi attempt và lý do chuyển kênh.

## Khi giải pháp trước vẫn hợp lý

Gọi một channel trực tiếp nếu business không yêu cầu fallback.

## Cách chạy

```bash
php before.php
php after.php
```

## Tài liệu liên quan

- [Readme](../../../production/notification-platform/README.md)
- [04 Idempotency](../../../handbook/microservices/05-idempotency.md)

## Tệp trong ví dụ

- [`before.php`](before.php): hiện thực baseline của **Notification fallback đa kênh**; dùng file này để tái hiện vấn đề “`before.php` dùng try/catch lồng nhau và lặp logic retry/fallback.”.
- [`after.php`](after.php): hiện thực hướng refactor “`after.php` tách channel strategy, policy phân loại lỗi và coordinator fallback.”; so sánh bằng output, invariant và failure behavior.
- `test.php` (nếu có): chạy contract/failure scenario được nêu trong “Điều cần quan sát”; test không nên chỉ assert concrete class được gọi.

## Sơ đồ tương tác của ví dụ

```mermaid
flowchart LR
    E0[Notification] --> E1[PrimaryChannel]
    E1[PrimaryChannel] --> E2[ClassifyFailure]
    E2[ClassifyFailure] --> E3[FallbackChannel]
```

## Kiểm thử tối thiểu

- Test permanent failure không retry và transient failure có budget.
- Test happy path không được thay thế failure test.
- Assertion cần kiểm tra state/side effect, không chỉ chuỗi output.
