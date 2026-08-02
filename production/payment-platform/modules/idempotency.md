# Idempotency

## Vai trò trong hệ thống

**Idempotency** là capability chuyên biệt của **payment platform**. Module chỉ sở hữu quyết định và dữ liệu cần thiết cho idempotency; nó không được biến thành service tổng hợp cho toàn platform. Input/output phải dùng ngôn ngữ nghiệp vụ, còn database, broker và provider được đặt sau port/adapter rõ ràng.

## Invariant cần bảo vệ

- **Một idempotency key chỉ đại diện cho một payload nghiệp vụ.**
- Mọi command có actor/correlation ID và chỉ được áp dụng một lần theo semantics của module.
- State transition phải kiểm tra precondition/version trước side effect bên ngoài.
- Correction không sửa projection trực tiếp; phải đi qua command hoặc reconciliation có audit.
- Tenant/currency/timezone/resource scope không được suy đoán ngầm.

## Thiết kế đề xuất

Đặt Idempotency Store trước mọi side effect. Mỗi key gắn với request fingerprint, trạng thái xử lý và response đã chuẩn hóa; cùng key khác payload phải bị từ chối, còn request đang xử lý phải trả trạng thái có thể retry an toàn.

```mermaid
sequenceDiagram
    participant A as Client
    participant B as PaymentAPI
    participant C as IdempotencyStore
    participant D as PaymentService
    participant E as Gateway
    A->>B: submit key + payload
    B->>C: claim or load record
    C->>D: execute once
    D->>E: persist stable response
    D->>E: replay same response
```


## Failure modes riêng của module

- key reuse với payload khác; race khi hai request cùng key; kết quả commit nhưng response mất.
- Dependency có thể thành công nhưng response/ack bị mất, vì vậy retry mù có thể nhân đôi side effect.
- Event có thể đến trễ, trùng hoặc sai thứ tự; consumer phải dùng version/idempotency để quyết định bỏ qua hay reconcile.
- Dữ liệu projection/cache có thể cũ hơn source of truth và không được dùng để thực hiện correction không kiểm chứng.

## Chiến lược kiểm thử

1. Unit test invariant: **Idempotency giữ trạng thái hợp lệ theo rule của payment-platform**.
2. State-transition test cho happy path, invalid transition và stale version.
3. Idempotency test: cùng key/cùng payload trả cùng result; cùng key/khác payload bị từ chối.
4. Integration test transaction + outbox/inbox nếu module vừa đổi state vừa publish event.
5. Concurrency/failure-injection test ở trước commit, sau commit và khi dependency timeout.
6. Reconciliation test chứng minh projection có thể rebuild từ source of truth.

## Observability

Theo dõi **duplicate-key conflict rate, replay hit rate, pending age**. Log/trace tối thiểu gồm resource ID, tenant, correlation/idempotency key, command/event type, version và provider nếu có.

Dashboard của **idempotency** trong payment platform phải hiển thị phạm vi ảnh hưởng, giá trị nghiệp vụ liên quan, tuổi của item lâu nhất và xu hướng backlog. Alert ưu tiên breach của invariant hoặc SLA riêng của idempotency; exception count chỉ là tín hiệu chẩn đoán phụ.

## Runbook

1. Xác định phạm vi ảnh hưởng theo resource/tenant/time window và dừng automation có thể làm sai lệch tăng thêm.
2. Xác minh source of truth, transition cuối cùng đã commit và side effect bên ngoài có thực sự xảy ra hay chưa.
3. Khóa key, đọc record/result đã commit, so hash payload, replay hoặc từ chối; không gọi provider lần hai.
4. Chạy verification query/contract check; mọi correction phải có actor, lý do và correlation ID.
5. Chỉ mở lại traffic/worker khi backlog, error rate và oldest pending age giảm ổn định.
6. Viết regression test hoặc guardrail tái hiện đúng failure vừa xảy ra.

## Câu hỏi design review

- Transaction boundary có đúng với invariant “Idempotency giữ trạng thái hợp lệ theo rule của payment-platform” không?
- Duplicate, stale version và out-of-order event được xử lý bằng semantics nào?
- Có đường reconcile khi dependency thành công nhưng response bị mất không?
- Metric **idempotency error rate; pending age; business impact** có phát hiện sai lệch trước khi khách hàng báo không?
- Runbook có thao tác an toàn, idempotent và có verification query không?

## Phạm vi tài liệu

Tài liệu này tập trung riêng vào **Idempotency** trong `production/payment-platform/modules/idempotency.md`: ownership, invariant, failure recovery và evidence vận hành. Overview của platform mô tả quan hệ giữa các capability; bài này là contract review cho module và không thay thế runbook triển khai cụ thể của môi trường.
