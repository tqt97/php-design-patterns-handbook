# ADR-025: Cache-aside cho product catalog

- **Trạng thái:** Accepted
- **Ngày:** 2026-08-01

## Bối cảnh

Read-heavy, dữ liệu chấp nhận stale ngắn.

## Quyết định

Cache-aside với TTL jitter và invalidation khi publish.

## Các lựa chọn đã cân nhắc

1. **Không thay đổi:** giữ cách hiện tại, chấp nhận rủi ro được mô tả trong bối cảnh “Read-heavy, dữ liệu chấp nhận stale ngắn.”.
2. **Giải pháp rộng hơn:** áp dụng abstraction/platform tổng quát cho nhiều use case, đổi lại tăng migration, ownership và chi phí vận hành.
3. **Quyết định của ADR:** Cache-aside với TTL jitter và invalidation khi publish.

Đối với **Cache-aside cho product catalog**, lựa chọn thứ ba thắng vì giải quyết đúng failure/change axis hiện tại và có đường kiểm chứng cụ thể. Nếu assumption về tải, ownership hoặc consistency thay đổi, ADR phải được mở lại thay vì tiếp tục mở rộng abstraction theo quán tính.

## Hệ quả

Có stale window và stampede control.

## Cách kiểm chứng

- Viết test trực tiếp cho invariant hoặc compatibility rule mà **Cache-aside cho product catalog** bảo vệ.
- Theo dõi một tín hiệu production gắn với bối cảnh: Read-heavy, dữ liệu chấp nhận stale ngắn.
- Thử migration/rollback trên dữ liệu hoặc traffic đại diện trước rollout toàn phần.
- Review ADR khi kết quả đo không cải thiện lead time, incident risk hoặc khả năng cô lập thay đổi như kỳ vọng.

## Khi cần xem xét lại

Xem xét lại quyết định **ADR-025: Cache-aside cho product catalog** khi tải thực tế, yêu cầu nhất quán, số biến thể triển khai hoặc ranh giới ownership thay đổi đủ lớn để làm các giả định ban đầu không còn đúng.

## Ghi chú triển khai

Quyết định này không tự động hợp thức hóa mọi trường hợp tương tự. Team triển khai phải ghi owner, phạm vi áp dụng, migration step và rollback trigger cho **Cache-aside cho product catalog**. Review code cần kiểm tra implementation có giữ đúng boundary “Cache-aside với TTL jitter và invalidation khi publish.” hay đã biến thành abstraction tổng quát ngoài phạm vi ADR.

## Decision drivers cụ thể

- Read-heavy workload, chấp nhận stale data và quyền sở hữu invalidation.
- Khả năng kiểm chứng bằng test và telemetry trước khi mở rộng phạm vi.
- Chi phí vận hành nếu phải duy trì hai đường xử lý trong thời gian migration.

## Rollout

Bật cache theo category nhỏ, so hit rate và stale incident, sau đó mở rộng dần; luôn giữ source read fallback.

## Rollback

Tắt cache bằng config, không xóa source path; flush key namespace khi serializer thay đổi.
