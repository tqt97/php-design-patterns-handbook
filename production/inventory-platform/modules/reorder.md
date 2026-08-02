# Reorder

## Vai trò trong hệ thống

**Reorder** là capability chuyên biệt của **inventory platform**. Module chỉ sở hữu quyết định và dữ liệu cần thiết cho reorder; nó không được biến thành service tổng hợp cho toàn platform. Input/output phải dùng ngôn ngữ nghiệp vụ, còn database, broker và provider được đặt sau port/adapter rõ ràng.

## Invariant cần bảo vệ

- **Reorder duy trì trạng thái hợp lệ và ownership rõ trong inventory platform.**
- Mọi command có actor/correlation ID và chỉ được áp dụng một lần theo semantics của module.
- State transition phải kiểm tra precondition/version trước side effect bên ngoài.
- Correction không sửa projection trực tiếp; phải đi qua command hoặc reconciliation có audit.
- Tenant/currency/timezone/resource scope không được suy đoán ngầm.

## Thiết kế đề xuất

Reorder engine sử dụng demand signal, lead time, safety stock và supplier constraint. Recommendation và purchase order là hai bước riêng để rule có thể giải thích và human approval khi rủi ro cao.

```mermaid
sequenceDiagram
    participant A as DemandProjection
    participant B as ReorderPolicy
    participant C as SupplierCatalog
    participant D as Buyer
    A->>B: refresh demand metrics
    B->>C: calculate reorder point
    C->>D: create recommendation
    C->>D: approve/select supplier
    C->>D: issue purchase order
```


## Failure modes riêng của module

- duplicate reorder; stale state; dependency timeout ở reorder.
- Dependency có thể thành công nhưng response/ack bị mất, vì vậy retry mù có thể nhân đôi side effect.
- Event có thể đến trễ, trùng hoặc sai thứ tự; consumer phải dùng version/idempotency để quyết định bỏ qua hay reconcile.
- Dữ liệu projection/cache có thể cũ hơn source of truth và không được dùng để thực hiện correction không kiểm chứng.

## Chiến lược kiểm thử

1. Unit test invariant: **Reorder giữ trạng thái hợp lệ theo rule của inventory-platform**.
2. State-transition test cho happy path, invalid transition và stale version.
3. Idempotency test: cùng key/cùng payload trả cùng result; cùng key/khác payload bị từ chối.
4. Integration test transaction + outbox/inbox nếu module vừa đổi state vừa publish event.
5. Concurrency/failure-injection test ở trước commit, sau commit và khi dependency timeout.
6. Reconciliation test chứng minh projection có thể rebuild từ source of truth.

## Observability

Theo dõi **reorder success rate, error rate, oldest pending age**. Log/trace tối thiểu gồm resource ID, tenant, correlation/idempotency key, command/event type, version và provider nếu có.

Dashboard của **reorder** trong inventory platform phải hiển thị phạm vi ảnh hưởng, giá trị nghiệp vụ liên quan, tuổi của item lâu nhất và xu hướng backlog. Alert ưu tiên breach của invariant hoặc SLA riêng của reorder; exception count chỉ là tín hiệu chẩn đoán phụ.

## Runbook

1. Xác định phạm vi ảnh hưởng theo resource/tenant/time window và dừng automation có thể làm sai lệch tăng thêm.
2. Xác minh source of truth, transition cuối cùng đã commit và side effect bên ngoài có thực sự xảy ra hay chưa.
3. Cô lập reorder, xác minh source of truth, replay command có idempotency key và kiểm tra kết quả.
4. Chạy verification query/contract check; mọi correction phải có actor, lý do và correlation ID.
5. Chỉ mở lại traffic/worker khi backlog, error rate và oldest pending age giảm ổn định.
6. Viết regression test hoặc guardrail tái hiện đúng failure vừa xảy ra.

## Câu hỏi design review

- Transaction boundary có đúng với invariant “Reorder giữ trạng thái hợp lệ theo rule của inventory-platform” không?
- Duplicate, stale version và out-of-order event được xử lý bằng semantics nào?
- Có đường reconcile khi dependency thành công nhưng response bị mất không?
- Metric **reorder error rate; pending age; business impact** có phát hiện sai lệch trước khi khách hàng báo không?
- Runbook có thao tác an toàn, idempotent và có verification query không?

## Phạm vi tài liệu

Tài liệu này tập trung riêng vào **Reorder** trong `production/inventory-platform/modules/reorder.md`: ownership, invariant, failure recovery và evidence vận hành. Overview của platform mô tả quan hệ giữa các capability; bài này là contract review cho module và không thay thế runbook triển khai cụ thể của môi trường.
