# State cho vòng đời tài liệu

## Câu chuyện nghiệp vụ

Tài liệu đi qua Draft → Review → Published → Archived. Mỗi trạng thái cho phép tập hành động khác nhau và có transition bất hợp lệ.

## Phiên bản ban đầu đang vướng gì?

`before.php` rải điều kiện trạng thái trong nhiều method. Việc thêm trạng thái mới làm tăng `if` và dễ bỏ sót rule.

## Ý tưởng refactor

`after.php` đặt hành vi và transition vào state object; `Document` giữ state hiện tại và ủy quyền.

## Cách đọc ví dụ

1. Đọc câu chuyện **State cho vòng đời tài liệu** và viết lại invariant nghiệp vụ bằng một câu; đừng bắt đầu từ tên pattern.
2. Chạy `before.php`, đối chiếu output với pain point: `before.php` rải điều kiện trạng thái trong nhiều method. Việc thêm trạng thái mới làm tăng `if` và dễ bỏ sót rule.
3. Vẽ dependency/flow hiện tại và đánh dấu nơi thay đổi hoặc failure lan sang client.
4. Chạy `after.php`, kiểm tra trọng tâm: State object sở hữu hành vi phụ thuộc trạng thái; context sở hữu identity và state hiện tại.
5. Mô phỏng tình huống phản chứng: Transition bất hợp lệ phải tạo lỗi nghiệp vụ rõ, không âm thầm bỏ qua. Sau đó giải thích vì sao refactor giảm blast radius và chi phí abstraction nào được thêm vào.

## Điều cần quan sát riêng của bài

- State object sở hữu hành vi phụ thuộc trạng thái; context sở hữu identity và state hiện tại.
- Transition bất hợp lệ phải tạo lỗi nghiệp vụ rõ, không âm thầm bỏ qua.
- Khác Strategy, State thường quyết định hoặc kiểm soát transition tiếp theo của lifecycle.

## Thực hành mở rộng

1. Thêm trạng thái `Scheduled` với thời điểm xuất bản.
2. Bổ sung guard: chỉ reviewer được approve.
3. Ghi lịch sử transition mà không để state object phụ thuộc database.

## Khi giải pháp trước vẫn hợp lý

Enum cùng transition table đủ tốt nếu mỗi trạng thái chỉ có một vài rule đơn giản.

## Cách chạy

```bash
php before.php
php after.php
```

## Tài liệu liên quan

- [08 State](../../../docs/03-behavioral/08-state.md)
- [Pattern Comparison](../../../cheatsheets/pattern-comparison.md)

## Tệp trong ví dụ

- [`before.php`](before.php): hiện thực baseline của **State cho vòng đời tài liệu**; dùng file này để tái hiện vấn đề “`before.php` rải điều kiện trạng thái trong nhiều method. Việc thêm trạng thái mới làm tăng `if` và dễ bỏ sót rule.”.
- [`after.php`](after.php): hiện thực hướng refactor “`after.php` đặt hành vi và transition vào state object; `Document` giữ state hiện tại và ủy quyền.”; so sánh bằng output, invariant và failure behavior.
- `test.php` (nếu có): chạy contract/failure scenario được nêu trong “Điều cần quan sát”; test không nên chỉ assert concrete class được gọi.
