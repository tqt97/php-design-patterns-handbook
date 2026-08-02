# Multi Tenancy

## Vai trò trong hệ thống

**Multi Tenancy** thuộc miền **platform** và chịu trách nhiệm cho một capability riêng, không phải service tổng hợp. **Multi Tenancy** là capability chuyên biệt trong **platform**. Boundary chỉ nhận input theo ngôn ngữ nghiệp vụ của multi tenancy, bảo vệ invariant và phát result/event ổn định. Persistence, broker, scheduler hoặc provider phải nằm sau port/adapter; module không được trở thành service tổng hợp cho toàn platform.

## Invariant cần bảo vệ

- tenant data và quota luôn cô lập.
- Mỗi command có idempotency/correlation key và kết quả ổn định khi retry.
- Transition quan trọng ghi actor, timestamp, version và lý do thay đổi.
- Không cập nhật trực tiếp projection để “sửa số”; mọi correction đi qua command có audit.

## Thiết kế đề xuất

Thiết kế tenant context tại ingress và truyền explicit qua application boundary. Isolation có thể shared-schema, schema-per-tenant hoặc database-per-tenant; mọi repository/cache/job key phải bao gồm tenant id và được kiểm tra bằng policy.

```mermaid
sequenceDiagram
    participant A as Gateway
    participant B as TenantResolver
    participant C as ApplicationService
    participant D as TenantAwareRepository
    participant E as Database
    A->>B: authenticate request
    B->>C: resolve tenant context
    C->>D: authorize membership
    D->>E: query with tenant scope
    D->>E: audit cross-tenant denial
```


## Failure modes riêng của module

- tenant context leak;
-  shared cache key;
-  noisy neighbor.
- Response bị mất sau khi side effect đã commit, khiến caller không biết nên retry hay reconcile.
- Event đến trễ hoặc sai thứ tự làm projection khác source of truth.

## Chiến lược kiểm thử

1. Unit test invariant: **tenant data và quota luôn cô lập**.
2. State-transition test cho happy path, invalid transition và stale version.
3. Idempotency test: cùng key/cùng payload trả cùng result; cùng key/khác payload bị từ chối.
4. Integration test transaction + outbox/inbox nếu module vừa đổi state vừa publish event.
5. Concurrency/failure-injection test ở trước commit, sau commit và khi dependency timeout.
6. Reconciliation test chứng minh projection có thể rebuild từ source of truth.

## Observability

Theo dõi **cross-tenant access denial; quota saturation**. Mọi log/trace phải có resource ID, correlation ID, command type và version. Alert nên dựa trên business impact và age của item chưa hoàn tất; exception count đơn thuần không đủ phân biệt sự cố với retry bình thường.

## Runbook

1. Xác định source of truth và transition cuối cùng đã commit.
2. Khoanh vùng theo resource ID, correlation ID, idempotency key và version.
3. Tạm dừng worker/retry nếu chúng có thể làm sai lệch tăng thêm.
4. disable affected route, rotate cache namespace và audit access.
5. Chạy verification query, ghi audit cho mọi correction và chỉ mở lại traffic khi metric trở về ngưỡng an toàn.
6. Bổ sung test/guardrail cho failure vừa xảy ra.

## Câu hỏi design review

- Transaction boundary có đúng với invariant “tenant data và quota luôn cô lập” không?
- Duplicate, stale version và out-of-order event được xử lý bằng semantics nào?
- Có đường reconcile khi dependency thành công nhưng response bị mất không?
- Metric **cross-tenant access denial; quota saturation** có phát hiện sai lệch trước khi khách hàng báo không?
- Runbook có thao tác an toàn, idempotent và có verification query không?

## Phạm vi tài liệu

Đây là **chuyên đề tình huống nâng cao** cho `production/platform/01-multi-tenancy.md`. Nội dung tập trung vào multi tenancy ở boundary này, không thay thế overview của platform.
