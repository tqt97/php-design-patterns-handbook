# Senior — Architecture, failure và migration

Bộ câu hỏi Senior tập trung transaction, concurrency, idempotency, migration, observability và cách pattern thay đổi failure surface của hệ thống production.

## 1. Khi nào không nên áp dụng pattern dù cấu trúc có vẻ phù hợp?

**Trả lời chi tiết:** Khi variation chưa có evidence, baseline trực tiếp dễ hiểu hơn, migration cost cao hoặc pattern không cải thiện correctness/testability/operability. Ghi revisit trigger thay vì speculative design.

**Cách ghi điểm:** Trình bày option “do nothing”.

**Câu hỏi đào sâu:** Với chủ đề **Khi nào không nên áp dụng pattern dù cấu trúc có vẻ phù hợp**, phân tích “Khi nào không nên áp dụng pattern dù cấu trúc có vẻ phù hợp” theo transaction boundary, concurrency, migration/rollback, telemetry và production evidence. Bổ sung transaction/concurrency, migration, rollback và telemetry production.

## 2. Đánh giá một abstraction bằng evidence nào?

**Trả lời chi tiết:** Change history, dependency graph, defect rate, test setup, lead time, performance profile và incident data. Abstraction tốt giảm blast radius hoặc bảo vệ semantics đo được.

**Cách ghi điểm:** Không dùng số interface làm metric.

**Câu hỏi đào sâu:** Với chủ đề **Đánh giá một abstraction bằng evidence nào**, phân tích “Đánh giá một abstraction bằng evidence nào” theo transaction boundary, concurrency, migration/rollback, telemetry và production evidence. Bổ sung transaction/concurrency, migration, rollback và telemetry production.

## 3. Pattern qua network boundary thay đổi ra sao?

**Trả lời chi tiết:** Call trở nên partial failure, latency, retry, duplication và versioning. Local Observer/Command cần outbox, idempotency, timeout và observability khi phân tán.

**Cách ghi điểm:** Nêu fallacy của distributed computing.

**Câu hỏi đào sâu:** Với chủ đề **Pattern qua network boundary thay đổi ra sao**, phân tích “Pattern qua network boundary thay đổi ra sao” theo transaction boundary, concurrency, migration/rollback, telemetry và production evidence. Bổ sung transaction/concurrency, migration, rollback và telemetry production.

## 4. Thiết kế idempotent Command thế nào?

**Trả lời chi tiết:** Operation key + actor/scope + payload fingerprint; reserve processing state atomically; lưu outcome; replay same payload; reject key conflict; reconciliation cho trạng thái kẹt.

**Cách ghi điểm:** Nhắc TTL chỉ khi semantics cho phép.

**Câu hỏi đào sâu:** Với chủ đề **Thiết kế idempotent Command thế nào**, phân biệt intent và fact, ownership của retry, versioning payload và cách tránh dùng event như RPC ẩn. Bổ sung transaction/concurrency, migration, rollback và telemetry production.

## 5. Aggregate boundary được xác định thế nào?

**Trả lời chi tiết:** Từ invariant cần atomicity, contention, transaction size và ownership. Rule không cần atomic có thể qua domain event/process manager.

**Cách ghi điểm:** Không gom theo UI screen.

**Câu hỏi đào sâu:** Với chủ đề **Aggregate boundary được xác định thế nào**, chọn invariant cần atomicity, phân tích kích thước aggregate và cách xử lý rule xuyên aggregate bằng process manager. Bổ sung transaction/concurrency, migration, rollback và telemetry production.

## 6. Generic Repository có vấn đề gì?

**Trả lời chi tiết:** CRUD generic làm mất domain semantics, leak query shape và tạo abstraction yếu. Repository cụ thể nên có method theo intent như `nextPendingSettlement()`.

**Cách ghi điểm:** Có thể dùng base infrastructure nội bộ nhưng không làm public domain API.

**Câu hỏi đào sâu:** Với chủ đề **Generic Repository có vấn đề gì**, định nghĩa collection semantics, aggregate boundary và transaction expectation; so sánh với Eloquent/query trực tiếp bằng một use case. Bổ sung transaction/concurrency, migration, rollback và telemetry production.

## 7. Event-driven design thất bại phổ biến ở đâu?

**Trả lời chi tiết:** Event dùng như RPC ẩn, schema không version, consumer không idempotent, không có replay/reconciliation và ownership mơ hồ.

**Cách ghi điểm:** Nêu event catalog và lag metric.

**Câu hỏi đào sâu:** Với chủ đề **Event-driven design thất bại phổ biến ở đâu**, phân biệt intent và fact, ownership của retry, versioning payload và cách tránh dùng event như RPC ẩn. Bổ sung transaction/concurrency, migration, rollback và telemetry production.

## 8. Khi nào dùng CQRS?

**Trả lời chi tiết:** Khi write invariant và read shape khác biệt rõ, query complexity/scale độc lập hoặc cần projection. Không dùng chỉ để tách folder Command/Query.

**Cách ghi điểm:** Nêu consistency và operational cost.

**Câu hỏi đào sâu:** Với chủ đề **Khi nào dùng CQRS**, chứng minh read/write model có lực thay đổi khác nhau; nêu consistency expectation và chi phí vận hành projection. Bổ sung transaction/concurrency, migration, rollback và telemetry production.

## 9. Đo performance của pattern thế nào?

**Trả lời chi tiết:** Xác định workload và budget, warm-up, nhiều sample, median/p95, resource profile và checksum behavior. Micro-benchmark chỉ đo overhead cục bộ, không kết luận maintainability.

**Cách ghi điểm:** Trình bày môi trường và noise.

**Câu hỏi đào sâu:** Với chủ đề **Đo performance của pattern thế nào**, định nghĩa workload đại diện, warm-up, percentile/checksum và điều gì benchmark không thể kết luận về maintainability. Bổ sung transaction/concurrency, migration, rollback và telemetry production.

## 10. Migration pattern an toàn cần gì?

**Trả lời chi tiết:** Characterization tests, seam, dual-run/shadow compare, cohort rollout, telemetry, rollback và cleanup date.

**Cách ghi điểm:** Nêu data migration/reconciliation.

**Câu hỏi đào sâu:** Với chủ đề **Migration pattern an toàn cần gì**, thiết kế expand–migrate–contract hoặc dual-run, xác định shadow diff, cutover gate và rollback trigger. Bổ sung transaction/concurrency, migration, rollback và telemetry production.

## 11. Anti-Corruption Layer khác Adapter đơn giản?

**Trả lời chi tiết:** ACL là boundary chiến lược có thể gồm nhiều adapter, translation model, policy và workflow để bảo vệ downstream model khỏi upstream semantics.

**Cách ghi điểm:** Áp dụng khi model mismatch đáng kể.

**Câu hỏi đào sâu:** Với chủ đề **Anti-Corruption Layer khác Adapter đơn giản**, liệt kê request, response, unit, enum và error cần dịch; thiết kế contract test với timeout, malformed response và vendor decline. Bổ sung transaction/concurrency, migration, rollback và telemetry production.

## 12. Saga orchestration hay choreography?

**Trả lời chi tiết:** Orchestration rõ flow/state/timeout nhưng central coordinator; choreography giảm trung tâm nhưng flow khó trace. Chọn theo độ phức tạp, ownership và observability.

**Cách ghi điểm:** Nêu process manager persistence.

**Câu hỏi đào sâu:** Với chủ đề **Saga orchestration hay choreography**, vẽ process state, timeout, compensation và manual intervention; giải thích cách deduplicate event và resume an toàn. Bổ sung transaction/concurrency, migration, rollback và telemetry production.

## 13. Outbox vẫn có thể mất hoặc trùng message không?

**Trả lời chi tiết:** Không mất giữa state/outbox nếu cùng transaction, nhưng publisher có thể gửi lặp khi crash sau publish trước mark. Consumer phải idempotent và có reconciliation.

**Cách ghi điểm:** At-least-once, không exactly-once end-to-end.

**Câu hỏi đào sâu:** Với chủ đề **Outbox vẫn có thể mất hoặc trùng message không**, phân tích “Outbox vẫn có thể mất hoặc trùng message không” theo transaction boundary, concurrency, migration/rollback, telemetry và production evidence. Bổ sung transaction/concurrency, migration, rollback và telemetry production.

## 14. Thiết kế error model xuyên layer thế nào?

**Trả lời chi tiết:** Domain error biểu diễn rejection; application map orchestration failure; adapter translate vendor exception; delivery map thành status/response. Giữ cause/correlation cho observability.

**Cách ghi điểm:** Không catch `Throwable` rồi trả false.

**Câu hỏi đào sâu:** Với chủ đề **Thiết kế error model xuyên layer thế nào**, phân tích “Thiết kế error model xuyên layer thế nào” theo transaction boundary, concurrency, migration/rollback, telemetry và production evidence. Bổ sung transaction/concurrency, migration, rollback và telemetry production.

## 15. Architecture fitness function nào có giá trị?

**Trả lời chi tiết:** Forbidden dependency/import, module cycles, schema compatibility, public API surface, event version rules và ownership checks. Rule phải tự động, ít false positive và có exception process.

**Cách ghi điểm:** Nối rule với risk cụ thể.

**Câu hỏi đào sâu:** Với chủ đề **Architecture fitness function nào có giá trị**, đề xuất một rule có thể chạy trong CI, chỉ ra false positive/exception process và metric cho architecture drift. Bổ sung transaction/concurrency, migration, rollback và telemetry production.