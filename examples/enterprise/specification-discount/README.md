# Specification cho điều kiện ưu đãi

## Câu chuyện nghiệp vụ

Khuyến mãi áp dụng khi khách VIP, đơn đạt ngưỡng và sản phẩm không thuộc danh sách loại trừ.

## Phiên bản ban đầu đang vướng gì?

`before.php` nhúng điều kiện dài trong service tính giá và lặp lại ở nhiều nơi.

## Ý tưởng refactor

`after.php` biểu diễn từng rule thành specification có thể kết hợp `and/or/not`.

## Cách đọc ví dụ

1. Đọc câu chuyện **Specification cho điều kiện ưu đãi** và viết lại invariant nghiệp vụ bằng một câu; đừng bắt đầu từ tên pattern.
2. Chạy `before.php`, đối chiếu output với pain point: `before.php` nhúng điều kiện dài trong service tính giá và lặp lại ở nhiều nơi.
3. Vẽ dependency/flow hiện tại và đánh dấu nơi thay đổi hoặc failure lan sang client.
4. Chạy `after.php`, kiểm tra trọng tâm: Specification biểu diễn predicate nghiệp vụ có tên, không phải mọi closure đều cần thành class.
5. Mô phỏng tình huống phản chứng: Composition phải giữ semantics rõ khi rule có lý do từ chối. Sau đó giải thích vì sao refactor giảm blast radius và chi phí abstraction nào được thêm vào.

## Điều cần quan sát riêng của bài

- Specification biểu diễn predicate nghiệp vụ có tên, không phải mọi closure đều cần thành class.
- Composition phải giữ semantics rõ khi rule có lý do từ chối.
- Pure specification dễ test; specification gọi database có chi phí và coupling khác.

## Thực hành mở rộng

1. Thêm rule ngày sinh nhật và kết hợp với VIP.
2. Trả lý do không đạt thay vì chỉ boolean.
3. Phân biệt specification trong memory với query specification.

## Khi giải pháp trước vẫn hợp lý

Điều kiện dùng một lần, ngắn và không có tên nghiệp vụ có thể để trực tiếp.

## Cách chạy

```bash
php before.php
php after.php
```

## Tài liệu liên quan

- [06 Specification](../../../docs/04-enterprise-patterns/04-specification.md)
- [Pattern Selection Guide](../../../cheatsheets/pattern-selection-guide.md)

## Tệp trong ví dụ

- [`before.php`](before.php): hiện thực baseline của **Specification cho điều kiện ưu đãi**; dùng file này để tái hiện vấn đề “`before.php` nhúng điều kiện dài trong service tính giá và lặp lại ở nhiều nơi.”.
- [`after.php`](after.php): hiện thực hướng refactor “`after.php` biểu diễn từng rule thành specification có thể kết hợp `and/or/not`.”; so sánh bằng output, invariant và failure behavior.
- `test.php` (nếu có): chạy contract/failure scenario được nêu trong “Điều cần quan sát”; test không nên chỉ assert concrete class được gọi.
