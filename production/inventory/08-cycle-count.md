# Cycle Count

## Vai trò trong hệ thống

**Cycle Count** thuộc miền **inventory** và chịu trách nhiệm cho một capability riêng, không phải service tổng hợp. **Cycle Count** là capability chuyên biệt trong **inventory**. Boundary chỉ nhận input theo ngôn ngữ nghiệp vụ của cycle count, bảo vệ invariant và phát result/event ổn định. Persistence, broker, scheduler hoặc provider phải nằm sau port/adapter; module không được trở thành service tổng hợp cho toàn platform.

## Invariant cần bảo vệ

- adjustment có bằng chứng và không xóa lịch sử.
- Mỗi command có idempotency/correlation key và kết quả ổn định khi retry.
- Transition quan trọng ghi actor, timestamp, version và lý do thay đổi.
- Không cập nhật trực tiếp projection để “sửa số”; mọi correction đi qua command có audit.

## Thiết kế đề xuất

Cycle count workflow tạo blind count session, khóa phạm vi hợp lý, so sánh với ledger snapshot và áp dụng variance policy. Adjustment cần reason/approver và luôn đi qua stock ledger.

```mermaid
sequenceDiagram
    participant A as WarehouseUser
    participant B as CountSession
    participant C as VariancePolicy
    participant D as Approval
    participant E as StockLedger
    A->>B: start session
    B->>C: capture physical count
    C->>D: compute variance
    D->>E: approve threshold breach
    D->>E: post adjustment
```


## Failure modes riêng của module

- count during movement;
-  wrong location;
-  duplicate adjustment.
- Response bị mất sau khi side effect đã commit, khiến caller không biết nên retry hay reconcile.
- Event đến trễ hoặc sai thứ tự làm projection khác source of truth.

## Chiến lược kiểm thử

1. Scenario test toàn quy trình count → variance review → approval → ledger adjustment, gồm blind count và recount khi variance vượt ngưỡng.
2. State-transition test cho happy path, invalid transition và stale version.
3. Idempotency test: cùng key/cùng payload trả cùng result; cùng key/khác payload bị từ chối.
4. Integration test transaction + outbox/inbox nếu module vừa đổi state vừa publish event.
5. Concurrency/failure-injection test ở trước commit, sau commit và khi dependency timeout.
6. Reconciliation test chứng minh projection có thể rebuild từ source of truth.

## Observability

Theo dõi **variance value; recount rate**. Mọi log/trace phải có resource ID, correlation ID, command type và version. Alert nên dựa trên business impact và age của item chưa hoàn tất; exception count đơn thuần không đủ phân biệt sự cố với retry bình thường.

## Runbook

1. Xác định source of truth và transition cuối cùng đã commit.
2. Khoanh vùng theo resource ID, correlation ID, idempotency key và version.
3. Tạm dừng worker/retry nếu chúng có thể làm sai lệch tăng thêm.
4. freeze location, reconcile open movements rồi post adjustment riêng.
5. Chạy verification query, ghi audit cho mọi correction và chỉ mở lại traffic khi metric trở về ngưỡng an toàn.
6. Bổ sung test/guardrail cho failure vừa xảy ra.

## Câu hỏi design review

- Ai phê duyệt variance theo giá trị/risk, và evidence nào chứng minh số tồn sau adjustment hội tụ với physical count?
- Duplicate, stale version và out-of-order event được xử lý bằng semantics nào?
- Có đường reconcile khi dependency thành công nhưng response bị mất không?
- Metric **variance value; recount rate** có phát hiện sai lệch trước khi khách hàng báo không?
- Runbook có thao tác an toàn, idempotent và có verification query không?

## Phạm vi tài liệu

Đây là **chuyên đề tình huống nâng cao** cho `production/inventory/08-cycle-count.md`. Nội dung tập trung vào cycle count ở boundary này, không thay thế overview của platform.
