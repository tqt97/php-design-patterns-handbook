# Pricing policies có thể kết hợp

## Câu chuyện nghiệp vụ

Giá cuối phụ thuộc giá nền, membership, campaign, coupon và tax; thứ tự áp dụng quyết định kết quả.

## Phiên bản ban đầu đang vướng gì?

`before.php` dùng một method dài với nhiều cờ.

## Ý tưởng refactor

`after.php` biểu diễn policy và composition có thứ tự rõ.

## Cách đọc ví dụ

1. Đọc câu chuyện **Pricing policies có thể kết hợp** và viết lại invariant nghiệp vụ bằng một câu; đừng bắt đầu từ tên pattern.
2. Chạy `before.php`, đối chiếu output với pain point: `before.php` dùng một method dài với nhiều cờ.
3. Vẽ dependency/flow hiện tại và đánh dấu nơi thay đổi hoặc failure lan sang client.
4. Chạy `after.php`, kiểm tra trọng tâm: Mỗi policy phải nêu nó thay đổi subtotal hay final total.
5. Mô phỏng tình huống phản chứng: Thứ tự và khả năng cộng dồn là invariant nghiệp vụ. Sau đó giải thích vì sao refactor giảm blast radius và chi phí abstraction nào được thêm vào.

## Điều cần quan sát riêng của bài

- Mỗi policy phải nêu nó thay đổi subtotal hay final total.
- Thứ tự và khả năng cộng dồn là invariant nghiệp vụ.
- Money và rounding phải nhất quán qua tất cả policy.

## Thực hành mở rộng

1. Thêm coupon không cộng dồn với campaign.
2. Trả breakdown để giải thích giá.
3. Property test: giá cuối không âm và deterministic.

## Khi giải pháp trước vẫn hợp lý

Công thức cố định, ít biến thể có thể rõ hơn trong một domain service.

## Cách chạy

```bash
php before.php
php after.php
```

## Tài liệu liên quan

- [09 Strategy](../../../docs/03-behavioral/09-strategy.md)
- [Domain Driven Design](../../../handbook/ddd/README.md)

## Tệp trong ví dụ

- [`before.php`](before.php): hiện thực baseline của **Pricing policies có thể kết hợp**; dùng file này để tái hiện vấn đề “`before.php` dùng một method dài với nhiều cờ.”.
- [`after.php`](after.php): hiện thực hướng refactor “`after.php` biểu diễn policy và composition có thứ tự rõ.”; so sánh bằng output, invariant và failure behavior.
- `test.php` (nếu có): chạy contract/failure scenario được nêu trong “Điều cần quan sát”; test không nên chỉ assert concrete class được gọi.

## Sơ đồ tương tác của ví dụ

```mermaid
flowchart LR
    E0[QuoteContext] --> E1[PolicySelector]
    E1[PolicySelector] --> E2[PricingPolicy]
    E2[PricingPolicy] --> E3[Quote]
```

## Kiểm thử tối thiểu

- Test policy version và shadow comparison trước rollout.
- Test happy path không được thay thế failure test.
- Assertion cần kiểm tra state/side effect, không chỉ chuỗi output.
