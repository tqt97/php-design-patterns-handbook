# Incident Packet

Template này gom evidence trước khi viết postmortem. Mục tiêu là giúp mọi người cùng nhìn một timeline, tránh tranh luận dựa trên ký ức và tách rõ dữ kiện khỏi giả thuyết.

## Impact

Mô tả customer-visible symptom, phạm vi tenant/region, số request hoặc giao dịch bị ảnh hưởng, thời lượng và mức độ vi phạm SLO. Tránh dùng từ chung chung như “hệ thống chậm” nếu có thể ghi percentile hoặc error rate.

## Detection

Ghi alert, ticket hoặc tín hiệu đầu tiên. Nêu detection delay và liệu alert có chỉ ra đúng capability hay chỉ là symptom ở lớp ngoài.

## Timeline (UTC)

| Time | Evidence | Interpretation |
|---|---|---|
| 00:00 | deploy marker / metric / trace | dữ kiện quan sát được, chưa kết luận nguyên nhân |

Mỗi mốc phải có nguồn: dashboard query, log link, trace ID, deployment ID hoặc command output. Dùng UTC để tránh nhầm timezone.

## Metrics và SLO

Ghi baseline trước incident, thời điểm deviation bắt đầu, peak, recovery và burn-rate. Nếu metric thiếu, ghi rõ khoảng trống observability thay vì suy đoán.

## Logs, traces và correlation IDs

Liệt kê query đã dùng, sample correlation ID đã redacted và call chain quan trọng. Phân biệt log từ application, queue, database và external provider.

## Hypotheses

| Hypothesis | Supporting evidence | Contradicting evidence | Experiment | Status |
|---|---|---|---|---|

Mỗi hypothesis phải có cách bác bỏ. Không đổi hypothesis thành “root cause” trước khi experiment hoặc evidence đủ mạnh.

## Containment

Ghi biện pháp giảm impact: feature flag, traffic shift, queue pause, dependency disable hoặc capacity increase. Nêu side effect và thời điểm rollback containment.

## Recovery và verification

Mô tả cách khôi phục state, reconciliation/backfill nếu cần và query xác minh không còn dữ liệu sai. Recovery hoàn thành khi business invariant được kiểm tra, không chỉ khi error rate giảm.

## Contributing factors

Phân loại yếu tố kỹ thuật, quy trình, observability và tổ chức. Tránh quy lỗi cá nhân; tập trung điều kiện khiến lỗi lọt qua hoặc khó phục hồi.

## Action items

| Action | Category | Owner | Due date | Verification |
|---|---|---|---|---|
| Ví dụ: thêm failure-injection tại checkpoint | prevention | team | YYYY-MM-DD | CI job/link test |

Category nên là prevention, detection, mitigation hoặc learning. Action “cẩn thận hơn” không đạt; action phải thay đổi code, test, guardrail, metric, runbook hoặc quy trình review.

## Review quality

- Timeline có nguồn evidence và UTC.
- Hypothesis có cả evidence ủng hộ và phản bác.
- Root contributing factors phân biệt với trigger.
- Action có owner, deadline và verification query.
- Packet liên kết ADR, source, test, dashboard và runbook liên quan.
