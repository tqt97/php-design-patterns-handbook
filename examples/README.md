# Examples — Học pattern qua tình huống chạy được

Thư mục này chứa các ví dụ nhỏ, mỗi ví dụ tập trung vào **một lực thay đổi cụ thể**. README trong từng ví dụ không dùng checklist chung: nó mô tả đúng bối cảnh, điểm cần quan sát, failure path và giới hạn của pattern đó.

## Cách học một ví dụ

1. Đọc “Câu chuyện nghiệp vụ” nhưng chưa mở `after.php`.
2. Chạy `before.php`, xác định code smell và invariant.
3. Tự phác thảo boundary trước khi xem lời giải.
4. Chạy `after.php`, so sánh dependency và testability.
5. Làm ít nhất một mục “Thực hành mở rộng”.

## Danh mục

### Creational

- [Factory Method cho exporter](creational/factory-exporter/README.md)
- [Builder tạo báo cáo](creational/builder-report/README.md)

### Structural

- [Adapter cho API thời tiết](structural/adapter-weather/README.md)
- [Decorator cấu hình đồ uống](structural/decorator-coffee/README.md)
- [Facade xử lý video](structural/facade-video/README.md)

### Behavioral

- [Strategy tính phí vận chuyển](behavioral/strategy-shipping/README.md)
- [State cho vòng đời tài liệu](behavioral/state-document/README.md)
- [Observer cho đơn hàng](behavioral/observer-order/README.md)
- [Chain phân loại ticket](behavioral/chain-support/README.md)

### Enterprise và framework

- [Repository cho Customer](enterprise/repository-customer/README.md)
- [Specification cho ưu đãi](enterprise/specification-discount/README.md)
- [Laravel Pipeline cho đơn hàng](laravel/pipeline-order/README.md)

### Advanced

- [Notification fallback](advanced/notification-fallback/README.md)
- [CRM Query Object](advanced/crm-query-object/README.md)
- [Idempotent Payment](advanced/idempotent-payment/README.md)
- [Audit Decorator](advanced/audit-decorator/README.md)
- [Webhook Inbox](advanced/webhook-inbox/README.md)
- [CSV Import Pipeline](advanced/csv-import-pipeline/README.md)
- [Booking State Machine](advanced/booking-state-machine/README.md)
- [Pricing Policies](advanced/pricing-policies/README.md)
- [Approval Chain](advanced/approval-chain/README.md)
- [Inventory Reservation](advanced/inventory-reservation/README.md)

## Tiêu chí chất lượng

Một ví dụ đạt yêu cầu khi `before` vẫn là lựa chọn hợp lý ở quy mô nhỏ, `after` giải quyết một lực thay đổi có thật, và README giải thích được cả lợi ích lẫn chi phí.
