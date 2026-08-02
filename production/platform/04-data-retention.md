# Data Retention

## Vai trò trong hệ thống

**Data Retention** thuộc miền **platform** và chịu trách nhiệm cho một capability riêng, không phải service tổng hợp. **Data Retention** là capability chuyên biệt trong **platform**. Boundary chỉ nhận input theo ngôn ngữ nghiệp vụ của data retention, bảo vệ invariant và phát result/event ổn định. Persistence, broker, scheduler hoặc provider phải nằm sau port/adapter; module không được trở thành service tổng hợp cho toàn platform.

## Invariant cần bảo vệ

- dữ liệu hết hạn được xóa/anonymize có bằng chứng.
- Mỗi command có idempotency/correlation key và kết quả ổn định khi retry.
- Transition quan trọng ghi actor, timestamp, version và lý do thay đổi.
- Không cập nhật trực tiếp projection để “sửa số”; mọi correction đi qua command có audit.

## Thiết kế đề xuất

Retention engine ánh xạ data class, jurisdiction, purpose và legal hold thành policy version. Scheduler tạo deletion/anonymization job; executor lưu evidence và retry an toàn, không xóa dữ liệu đang bị hold.

```mermaid
sequenceDiagram
    participant A as DataCatalog
    participant B as RetentionPlanner
    participant C as LegalHoldStore
    participant D as DeletionWorker
    participant E as EvidenceStore
    A->>B: classify dataset
    B->>C: compute due records
    C->>D: exclude legal holds
    D->>E: delete/anonymize
    D->>E: record completion evidence
```


## Failure modes riêng của module

- legal hold ignored;
-  partial deletion;
-  replica lag.
- Response bị mất sau khi side effect đã commit, khiến caller không biết nên retry hay reconcile.
- Event đến trễ hoặc sai thứ tự làm projection khác source of truth.

## Chiến lược kiểm thử

1. Unit test invariant: **dữ liệu hết hạn được xóa/anonymize có bằng chứng**.
2. State-transition test cho happy path, invalid transition và stale version.
3. Idempotency test: cùng key/cùng payload trả cùng result; cùng key/khác payload bị từ chối.
4. Integration test transaction + outbox/inbox nếu module vừa đổi state vừa publish event.
5. Concurrency/failure-injection test ở trước commit, sau commit và khi dependency timeout.
6. Reconciliation test chứng minh projection có thể rebuild từ source of truth.

## Observability

Theo dõi **expired rows; deletion lag**. Mọi log/trace phải có resource ID, correlation ID, command type và version. Alert nên dựa trên business impact và age của item chưa hoàn tất; exception count đơn thuần không đủ phân biệt sự cố với retry bình thường.

## Runbook

1. Xác định source of truth và transition cuối cùng đã commit.
2. Khoanh vùng theo resource ID, correlation ID, idempotency key và version.
3. Tạm dừng worker/retry nếu chúng có thể làm sai lệch tăng thêm.
4. pause deletion, verify hold set và rerun idempotent erasure.
5. Chạy verification query, ghi audit cho mọi correction và chỉ mở lại traffic khi metric trở về ngưỡng an toàn.
6. Bổ sung test/guardrail cho failure vừa xảy ra.

## Câu hỏi design review

- Transaction boundary có đúng với invariant “dữ liệu hết hạn được xóa/anonymize có bằng chứng” không?
- Duplicate, stale version và out-of-order event được xử lý bằng semantics nào?
- Có đường reconcile khi dependency thành công nhưng response bị mất không?
- Metric **expired rows; deletion lag** có phát hiện sai lệch trước khi khách hàng báo không?
- Runbook có thao tác an toàn, idempotent và có verification query không?

## Phạm vi tài liệu

Đây là **chuyên đề tình huống nâng cao** cho `production/platform/04-data-retention.md`. Nội dung tập trung vào data retention ở boundary này, không thay thế overview của platform.
