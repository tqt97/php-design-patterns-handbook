# Quiz — 02 Unit Of Work Outbox

## 1. Unit of Work sở hữu gì?

**Đáp án gợi ý:** Transaction boundary, commit/rollback và coordination giữa repository trong cùng store.

## 2. Outbox giải quyết lỗi nào?

**Đáp án gợi ý:** Dual-write database + broker bằng cách ghi event record cùng state transaction.

## 3. Outbox có exactly-once không?

**Đáp án gợi ý:** Không; publish có thể duplicate, consumer phải idempotent/inbox.

## 4. Crash sau broker ack trước mark?

**Đáp án gợi ý:** Message vẫn pending và publish lại; event ID giúp dedupe.

## 5. Nested transaction xử lý sao?

**Đáp án gợi ý:** Contract phải rõ savepoint/no-op/forbid; không để behavior ngầm.

## 6. Metric outbox quan trọng?

**Đáp án gợi ý:** Oldest pending age, backlog, publish failure, retry/dead-letter.

## 7. Ordering đảm bảo mức nào?

**Đáp án gợi ý:** Thường theo aggregate/partition nếu thiết kế; global order hiếm và đắt.

## 8. Khi không cần Outbox?

**Đáp án gợi ý:** Side effect cùng database transaction hoặc event không cần reliable delivery.

## Cách sử dụng kết quả

- Nếu dưới 4 câu: quay lại diagram của **02 unit of work outbox**, chạy `demo.php` và ghi lại một misunderstanding cụ thể.
- Nếu đạt 4–6 câu: hoàn thành exercise, yêu cầu peer chỉ ra một failure path hoặc trade-off còn thiếu.
- Nếu đạt 7–8 câu: tự thiết kế một biến thể production của **02 unit of work outbox**, gồm test, metric và điều kiện rollback/revisit.
