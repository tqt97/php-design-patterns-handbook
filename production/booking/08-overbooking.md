# Overbooking

## Vai trò trong hệ thống

**Overbooking** thuộc miền **booking** và chịu trách nhiệm cho một capability riêng, không phải service tổng hợp. **Overbooking** là capability chuyên biệt trong **booking**. Boundary chỉ nhận input theo ngôn ngữ nghiệp vụ của overbooking, bảo vệ invariant và phát result/event ổn định. Persistence, broker, scheduler hoặc provider phải nằm sau port/adapter; module không được trở thành service tổng hợp cho toàn platform.

## Invariant cần bảo vệ

- confirmed booking không vượt capacity policy.
- Mỗi command có idempotency/correlation key và kết quả ổn định khi retry.
- Transition quan trọng ghi actor, timestamp, version và lý do thay đổi.
- Không cập nhật trực tiếp projection để “sửa số”; mọi correction đi qua command có audit.

## Thiết kế đề xuất

Giữ invariant capacity bằng conditional reservation trên resource/time bucket. Search có thể eventual consistent, nhưng confirm sử dụng version/lock; conflict trả lỗi nghiệp vụ rõ ràng thay vì retry vô hạn.

```mermaid
sequenceDiagram
    participant A as BookingCommand
    participant B as CapacityRepository
    participant C as OverlapChecker
    participant D as BookingRepository
    A->>B: request interval
    B->>C: load overlapping commitments
    C->>D: validate capacity
    C->>D: conditional commit
    C->>D: return conflict or booking
```


## Failure modes riêng của module

- concurrent confirmation;
-  stale inventory;
-  manual override.
- Response bị mất sau khi side effect đã commit, khiến caller không biết nên retry hay reconcile.
- Event đến trễ hoặc sai thứ tự làm projection khác source of truth.

## Chiến lược kiểm thử

1. Scenario test oversell theo room/resource/date, gồm hold expiry, cancellation race, manual override và reconciliation với channel manager.
2. State-transition test cho happy path, invalid transition và stale version.
3. Idempotency test: cùng key/cùng payload trả cùng result; cùng key/khác payload bị từ chối.
4. Integration test transaction + outbox/inbox nếu module vừa đổi state vừa publish event.
5. Concurrency/failure-injection test ở trước commit, sau commit và khi dependency timeout.
6. Reconciliation test chứng minh projection có thể rebuild từ source of truth.

## Observability

Theo dõi **oversell count; compensation cost**. Mọi log/trace phải có resource ID, correlation ID, command type và version. Alert nên dựa trên business impact và age của item chưa hoàn tất; exception count đơn thuần không đủ phân biệt sự cố với retry bình thường.

## Runbook

1. Xác định source of truth và transition cuối cùng đã commit.
2. Khoanh vùng theo resource ID, correlation ID, idempotency key và version.
3. Tạm dừng worker/retry nếu chúng có thể làm sai lệch tăng thêm.
4. khóa inventory bucket, xác định winners và chạy compensation có audit.
5. Chạy verification query, ghi audit cho mọi correction và chỉ mở lại traffic khi metric trở về ngưỡng an toàn.
6. Bổ sung test/guardrail cho failure vừa xảy ra.

## Câu hỏi design review

- Khi external channel xác nhận muộn, hệ thống chọn reject, waitlist hay manual reaccommodation và metric nào kích hoạt runbook?
- Duplicate, stale version và out-of-order event được xử lý bằng semantics nào?
- Có đường reconcile khi dependency thành công nhưng response bị mất không?
- Metric **oversell count; compensation cost** có phát hiện sai lệch trước khi khách hàng báo không?
- Runbook có thao tác an toàn, idempotent và có verification query không?

## Phạm vi tài liệu

Đây là **chuyên đề tình huống nâng cao** cho `production/booking/08-overbooking.md`. Nội dung tập trung vào overbooking ở boundary này, không thay thế overview của platform.
