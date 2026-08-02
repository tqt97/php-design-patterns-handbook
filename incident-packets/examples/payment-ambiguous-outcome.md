# Sample Incident: Payment Ambiguous Outcome

## Impact

12 checkout request hiển thị thất bại dù provider đã capture. Không ghi nhận duplicate charge vì retry bị chặn bởi idempotency key.

## Timeline (UTC)

| Time | Evidence | Interpretation |
|---|---|---|
| 10:00 | deploy marker | phiên bản mới bắt đầu |
| 10:04 | provider success tăng, order paid không tăng | failure giữa provider và persistence |
| 10:07 | ambiguous outcome alert | detection hoạt động |
| 10:12 | reconciliation worker backfill | khôi phục state |

## Root contributing factors

Persistence timeout xảy ra sau provider success; API cũ map timeout thành retryable thay vì ambiguous. Thiếu dashboard so sánh provider capture và internal payment state.

## Actions

- Map post-provider timeout thành `Ambiguous`.
- Thêm reconciliation work item và metric lag.
- Failure-injection test tại checkpoint `after-provider-before-persist`.
- Runbook xác minh provider bằng idempotency key trước retry.
