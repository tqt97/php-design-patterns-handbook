# Quiz — 03 Observer State

## 1. Observer phát command hay fact?

**Đáp án gợi ý:** Nên phát fact đã xảy ra; command có một owner xử lý rõ.

## 2. Event nên phát khi nào?

**Đáp án gợi ý:** Sau khi state transition hợp lệ; side effect external nên after-commit/outbox.

## 3. State pattern bảo vệ gì?

**Đáp án gợi ý:** Transition/behavior theo lifecycle, ngăn operation bất hợp lệ rải trong `if`.

## 4. Duplicate event xử lý thế nào?

**Đáp án gợi ý:** Subscriber idempotent hoặc inbox/natural key; at-least-once là mặc định thực tế.

## 5. Subscriber lỗi có rollback aggregate không?

**Đáp án gợi ý:** Tùy semantics; với side effect async không nên làm transaction gốc thất bại sau commit.

## 6. Test State hiệu quả?

**Đáp án gợi ý:** Transition table gồm happy, illegal và stale version; assert state/event.

## 7. Observer khác Mediator?

**Đáp án gợi ý:** Observer broadcast fact đến subscriber độc lập; Mediator điều phối interaction cụ thể.

## 8. Failure cần quan sát?

**Đáp án gợi ý:** Out-of-order, duplicate, listener lag và state stuck; có metric/runbook.

## Cách sử dụng kết quả

- Nếu dưới 4 câu: quay lại diagram của **03 observer state**, chạy `demo.php` và ghi lại một misunderstanding cụ thể.
- Nếu đạt 4–6 câu: hoàn thành exercise, yêu cầu peer chỉ ra một failure path hoặc trade-off còn thiếu.
- Nếu đạt 7–8 câu: tự thiết kế một biến thể production của **03 observer state**, gồm test, metric và điều kiện rollback/revisit.
