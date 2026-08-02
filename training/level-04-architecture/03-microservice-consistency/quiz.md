# Quiz — 03 Microservice Consistency

## 1. Saga giải quyết gì?

**Đáp án gợi ý:** Workflow nhiều local transaction bằng state, command/event và compensation.

## 2. Orchestration vs choreography?

**Đáp án gợi ý:** Orchestrator có state/flow rõ; choreography giảm trung tâm nhưng khó nhìn toàn workflow.

## 3. Compensation có phải rollback?

**Đáp án gợi ý:** Không; là business action bù, có thể thất bại và không khôi phục tuyệt đối.

## 4. Idempotency key đặt đâu?

**Đáp án gợi ý:** Theo logical command/side effect tại service boundary, lưu payload fingerprint/result.

## 5. Out-of-order xử lý thế nào?

**Đáp án gợi ý:** Version/state guard, buffer hoặc reject + reconciliation.

## 6. Khi nên dùng transaction đồng bộ?

**Đáp án gợi ý:** Nếu capability cùng service/store và strong consistency khả thi, tránh phân tán không cần thiết.

## 7. Metric quan trọng?

**Đáp án gợi ý:** Stuck saga age, compensation failure, duplicate, lag và manual intervention.

## 8. Failure rehearsal?

**Đáp án gợi ý:** Timeout, duplicate, partial success, dependency unavailable và operator recovery.

## Cách sử dụng kết quả

- Nếu dưới 4 câu: quay lại diagram của **03 microservice consistency**, chạy `demo.php` và ghi lại một misunderstanding cụ thể.
- Nếu đạt 4–6 câu: hoàn thành exercise, yêu cầu peer chỉ ra một failure path hoặc trade-off còn thiếu.
- Nếu đạt 7–8 câu: tự thiết kế một biến thể production của **03 microservice consistency**, gồm test, metric và điều kiện rollback/revisit.
