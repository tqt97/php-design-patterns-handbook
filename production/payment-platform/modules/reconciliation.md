# Reconciliation

## Vai trò trong hệ thống

**Reconciliation** là capability chuyên biệt của **payment platform**. Module chỉ sở hữu quyết định và dữ liệu cần thiết cho reconciliation; nó không được biến thành service tổng hợp cho toàn platform. Input/output phải dùng ngôn ngữ nghiệp vụ, còn database, broker và provider được đặt sau port/adapter rõ ràng.

## Invariant cần bảo vệ

- **Mọi giao dịch nội bộ phải map được với settlement/provider record.**
- Mọi command có actor/correlation ID và chỉ được áp dụng một lần theo semantics của module.
- State transition phải kiểm tra precondition/version trước side effect bên ngoài.
- Correction không sửa projection trực tiếp; phải đi qua command hoặc reconciliation có audit.
- Tenant/currency/timezone/resource scope không được suy đoán ngầm.

## Thiết kế đề xuất

Tách ingestion, normalization, matching và exception workflow. Dữ liệu PSP được lưu nguyên bản để audit; matcher dùng nhiều khóa và tolerance; unmatched item đi vào exception queue có owner và SLA.

```mermaid
sequenceDiagram
    participant A as PSPFile
    participant B as Normalizer
    participant C as Matcher
    participant D as InternalLedger
    participant E as ExceptionQueue
    A->>B: ingest immutable source
    B->>C: normalize identifiers/amounts
    C->>D: match against ledger
    D->>E: classify mismatch
    D->>E: assign investigation
```


## Failure modes riêng của module

- provider file thiếu/trùng; lệch amount/currency; late settlement.
- Dependency có thể thành công nhưng response/ack bị mất, vì vậy retry mù có thể nhân đôi side effect.
- Event có thể đến trễ, trùng hoặc sai thứ tự; consumer phải dùng version/idempotency để quyết định bỏ qua hay reconcile.
- Dữ liệu projection/cache có thể cũ hơn source of truth và không được dùng để thực hiện correction không kiểm chứng.

## Chiến lược kiểm thử

1. Contract test matcher theo provider reference, amount, currency và time window; ambiguous match phải vào exception queue thay vì tự ghép.
2. State-transition test cho happy path, invalid transition và stale version.
3. Idempotency test: cùng key/cùng payload trả cùng result; cùng key/khác payload bị từ chối.
4. Integration test transaction + outbox/inbox nếu module vừa đổi state vừa publish event.
5. Concurrency/failure-injection test ở trước commit, sau commit và khi dependency timeout.
6. Reconciliation test chứng minh projection có thể rebuild từ source of truth.

## Observability

Theo dõi **unmatched count/value, oldest unmatched age**. Log/trace tối thiểu gồm resource ID, tenant, correlation/idempotency key, command/event type, version và provider nếu có.

Dashboard của **reconciliation** trong payment platform phải hiển thị phạm vi ảnh hưởng, giá trị nghiệp vụ liên quan, tuổi của item lâu nhất và xu hướng backlog. Alert ưu tiên breach của invariant hoặc SLA riêng của reconciliation; exception count chỉ là tín hiệu chẩn đoán phụ.

## Runbook

1. Xác định phạm vi ảnh hưởng theo resource/tenant/time window và dừng automation có thể làm sai lệch tăng thêm.
2. Xác minh source of truth, transition cuối cùng đã commit và side effect bên ngoài có thực sự xảy ra hay chưa.
3. Đóng băng auto-correction, phân loại mismatch, re-import idempotently, phê duyệt adjustment.
4. Chạy verification query/contract check; mọi correction phải có actor, lý do và correlation ID.
5. Chỉ mở lại traffic/worker khi backlog, error rate và oldest pending age giảm ổn định.
6. Viết regression test hoặc guardrail tái hiện đúng failure vừa xảy ra.

## Câu hỏi design review

- Matching rule/version nào đã tạo kết quả, và rerun cùng settlement file có tạo duplicate adjustment không?
- Duplicate, stale version và out-of-order event được xử lý bằng semantics nào?
- Có đường reconcile khi dependency thành công nhưng response bị mất không?
- Metric **unmatched count; unmatched amount; oldest unmatched age** có phát hiện sai lệch trước khi khách hàng báo không?
- Runbook có thao tác an toàn, idempotent và có verification query không?

## Phạm vi tài liệu

Tài liệu này tập trung riêng vào **Reconciliation** trong `production/payment-platform/modules/reconciliation.md`: ownership, invariant, failure recovery và evidence vận hành. Overview của platform mô tả quan hệ giữa các capability; bài này là contract review cho module và không thay thế runbook triển khai cụ thể của môi trường.
