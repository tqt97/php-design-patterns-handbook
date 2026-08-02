# Quiz — 02 Adapter Decorator

## 1. Adapter dịch những gì?

**Đáp án gợi ý:** Request/response type, units, status và technical exception sang stable application contract.

## 2. Decorator thêm behavior thế nào?

**Đáp án gợi ý:** Bọc cùng contract và delegate; wrapper order là behavior phải test.

## 3. Timeout sau provider success map ra sao?

**Đáp án gợi ý:** Thành Unknown/ambiguous, không Declined; cần idempotency/reconciliation.

## 4. Retry và idempotency nên xếp thế nào?

**Đáp án gợi ý:** Idempotency bảo vệ logical operation; retry chỉ lặp temporary attempt mà không tạo side effect kép.

## 5. Adapter khác Facade?

**Đáp án gợi ý:** Adapter đổi interface; Facade đơn giản hóa nhiều subsystem mà không nhất thiết dịch contract.

## 6. Test Adapter tốt gồm gì?

**Đáp án gợi ý:** Fixture mapping, contract test, unknown status, timeout và redaction.

## 7. Decorator nào không nên có?

**Đáp án gợi ý:** Concern cần shared workflow state/transaction coordination; nên ở application service/pipeline.

## 8. Cách debug chain?

**Đáp án gợi ý:** Log wrapper chain, call count, attempt ID và error classification theo layer.

## Cách sử dụng kết quả

- Nếu dưới 4 câu: quay lại diagram của **02 adapter decorator**, chạy `demo.php` và ghi lại một misunderstanding cụ thể.
- Nếu đạt 4–6 câu: hoàn thành exercise, yêu cầu peer chỉ ra một failure path hoặc trade-off còn thiếu.
- Nếu đạt 7–8 câu: tự thiết kế một biến thể production của **02 adapter decorator**, gồm test, metric và điều kiện rollback/revisit.
