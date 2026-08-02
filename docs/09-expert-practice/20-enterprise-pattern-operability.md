# Enterprise Pattern Operability

Một pattern chỉ “enterprise-ready” khi team vận hành được failure mà pattern tạo ra. Strategy tạo rủi ro chọn sai policy; Observer tạo duplicate và ordering; Repository có thể che query cost; Outbox tạo backlog và reconciliation.

## Mô hình operability

```mermaid
flowchart LR
    DesignDecision --> FailureMode --> Signal --> Alert --> Runbook --> Recovery --> Learning
```

## Scorecard

| Khía cạnh | Câu hỏi |
|---|---|
| Ownership | Ai chịu trách nhiệm khi abstraction lỗi? |
| Signal | Metric/log/trace nào phát hiện lỗi trước khách hàng? |
| Containment | Có giới hạn blast radius theo tenant/aggregate/provider không? |
| Recovery | Retry, compensate, reconcile hay manual action? |
| Reversibility | Có kill switch hoặc đường quay về baseline không? |

## Failure walkthrough

Giả sử Strategy chọn nhầm pricing policy cho một tenant. Failure không nằm ở interface mà ở selection metadata. Thiết kế tốt phải log policy version, tenant, input fingerprint và decision result; metric phải phát hiện mismatch qua shadow comparison; rollback phải chuyển selector về baseline mà không deploy lại.

## Bài tập

Chọn một pattern đang dùng trong dự án. Lập failure matrix gồm nguyên nhân, triệu chứng, signal, owner, recovery và rollback. Nếu không thể viết runbook ngắn, abstraction chưa đủ rõ để vận hành.

## Review packet đề xuất

Khi review một abstraction, đính kèm failure matrix, dashboard screenshot, runbook link và kết quả drill. Với Strategy, cần policy-selection telemetry; với Outbox, cần backlog age và duplicate evidence; với Repository, cần query plan/read-model impact. Packet giúp reviewer đánh giá khả năng vận hành thay vì chỉ xem class diagram.

## Ví dụ đánh giá Repository

Repository có thể làm application code dễ đọc hơn nhưng đồng thời che query cost và lazy loading. Review packet nên chứa query plan, số aggregate được tải, latency percentile và lỗi version conflict. Nếu use case chỉ đọc projection, Query Object thường tạo signal vận hành rõ hơn. Nếu repository lưu aggregate, metric phải theo dõi conflict, transaction duration và lỗi mapping thay vì chỉ đếm số lần gọi method.
