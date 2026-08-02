# Audit Log

## Vai trò trong hệ thống

**Audit Log** thuộc miền **platform** và chịu trách nhiệm cho một capability riêng, không phải service tổng hợp. **Audit Log** là capability chuyên biệt trong **platform**. Boundary chỉ nhận input theo ngôn ngữ nghiệp vụ của audit log, bảo vệ invariant và phát result/event ổn định. Persistence, broker, scheduler hoặc provider phải nằm sau port/adapter; module không được trở thành service tổng hợp cho toàn platform.

## Invariant cần bảo vệ

- audit event bất biến, có actor và correlation.
- Mỗi command có idempotency/correlation key và kết quả ổn định khi retry.
- Transition quan trọng ghi actor, timestamp, version và lý do thay đổi.
- Không cập nhật trực tiếp projection để “sửa số”; mọi correction đi qua command có audit.

## Thiết kế đề xuất

Audit platform cung cấp append-only record, schema version, redaction và tamper evidence. Producer gửi structured audit event; ingestion xác thực, enrich identity và lưu immutable; query bị authorization và retention policy kiểm soát.

```mermaid
sequenceDiagram
    participant A as Service
    participant B as AuditIngestor
    participant C as SchemaRegistry
    participant D as ImmutableStore
    participant E as AuditQuery
    A->>B: submit structured event
    B->>C: validate/enrich
    C->>D: append immutable record
    D->>E: index safe fields
    D->>E: query with authorization
```


## Failure modes riêng của module

- missing event;
-  tamper;
-  PII leakage.
- Response bị mất sau khi side effect đã commit, khiến caller không biết nên retry hay reconcile.
- Event đến trễ hoặc sai thứ tự làm projection khác source of truth.

## Chiến lược kiểm thử

1. Unit test invariant: **audit event bất biến, có actor và correlation**.
2. State-transition test cho happy path, invalid transition và stale version.
3. Idempotency test: cùng key/cùng payload trả cùng result; cùng key/khác payload bị từ chối.
4. Integration test transaction + outbox/inbox nếu module vừa đổi state vừa publish event.
5. Concurrency/failure-injection test ở trước commit, sau commit và khi dependency timeout.
6. Reconciliation test chứng minh projection có thể rebuild từ source of truth.

## Observability

Theo dõi **audit gap; ingestion lag**. Mọi log/trace phải có resource ID, correlation ID, command type và version. Alert nên dựa trên business impact và age của item chưa hoàn tất; exception count đơn thuần không đủ phân biệt sự cố với retry bình thường.

## Runbook

1. Xác định source of truth và transition cuối cùng đã commit.
2. Khoanh vùng theo resource ID, correlation ID, idempotency key và version.
3. Tạm dừng worker/retry nếu chúng có thể làm sai lệch tăng thêm.
4. seal writer, export evidence và backfill từ source event.
5. Chạy verification query, ghi audit cho mọi correction và chỉ mở lại traffic khi metric trở về ngưỡng an toàn.
6. Bổ sung test/guardrail cho failure vừa xảy ra.

## Câu hỏi design review

- Transaction boundary có đúng với invariant “audit event bất biến, có actor và correlation” không?
- Duplicate, stale version và out-of-order event được xử lý bằng semantics nào?
- Có đường reconcile khi dependency thành công nhưng response bị mất không?
- Metric **audit gap; ingestion lag** có phát hiện sai lệch trước khi khách hàng báo không?
- Runbook có thao tác an toàn, idempotent và có verification query không?

## Phạm vi tài liệu

Đây là **chuyên đề tình huống nâng cao** cho `production/platform/03-audit-log.md`. Nội dung tập trung vào audit log ở boundary này, không thay thế overview của platform.
