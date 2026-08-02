# Quiz — 03 Production Readiness

## 1. Go/no-go cần evidence gì?

**Đáp án gợi ý:** Correctness tests, migration rehearsal, capacity, observability, rollback và owner/on-call.

## 2. SLO khác alert?

**Đáp án gợi ý:** SLO là mục tiêu service; alert dựa symptom/burn rate cần hành động, không mọi metric.

## 3. Rollback có luôn đơn giản?

**Đáp án gợi ý:** Không khi schema/event/data đã migrate; cần forward-fix/compatibility plan.

## 4. Failure injection chọn gì?

**Đáp án gợi ý:** Failure có xác suất/impact cao tại dependency/transaction boundary, không random chaos vô mục tiêu.

## 5. Canary metric?

**Đáp án gợi ý:** Business outcome, error/latency, resource và invariant mismatch so control.

## 6. Runbook tốt?

**Đáp án gợi ý:** Trigger, diagnosis, containment, recovery, verification, escalation và evidence capture.

## 7. Post-release verification?

**Đáp án gợi ý:** So expected vs actual, check queue/backlog/data consistency và rollback trigger window.

## 8. Cách ghi điểm Tech Lead?

**Đáp án gợi ý:** Liên kết design với operability, ownership, reversible rollout và learning loop.

## Cách sử dụng kết quả

- Nếu dưới 4 câu: quay lại diagram của **03 production readiness**, chạy `demo.php` và ghi lại một misunderstanding cụ thể.
- Nếu đạt 4–6 câu: hoàn thành exercise, yêu cầu peer chỉ ra một failure path hoặc trade-off còn thiếu.
- Nếu đạt 7–8 câu: tự thiết kế một biến thể production của **03 production readiness**, gồm test, metric và điều kiện rollback/revisit.
