# ADR-032: Version Integration Event

- **Trạng thái:** Accepted
- **Ngày:** 2026-08-01

## Bối cảnh

Consumer deploy độc lập và schema có thể đổi.

## Quyết định

Backward-compatible evolution; breaking change tạo version mới.

## Các lựa chọn đã cân nhắc

1. **Không thay đổi:** giữ cách hiện tại, chấp nhận rủi ro được mô tả trong bối cảnh “Consumer deploy độc lập và schema có thể đổi.”.
2. **Giải pháp rộng hơn:** áp dụng abstraction/platform tổng quát cho nhiều use case, đổi lại tăng migration, ownership và chi phí vận hành.
3. **Quyết định của ADR:** Backward-compatible evolution; breaking change tạo version mới.

Đối với **Version Integration Event**, lựa chọn thứ ba thắng vì giải quyết đúng failure/change axis hiện tại và có đường kiểm chứng cụ thể. Nếu assumption về tải, ownership hoặc consistency thay đổi, ADR phải được mở lại thay vì tiếp tục mở rộng abstraction theo quán tính.

## Hệ quả

Phải vận hành song song và theo dõi consumer.

## Cách kiểm chứng

- Viết test trực tiếp cho invariant hoặc compatibility rule mà **Version Integration Event** bảo vệ.
- Theo dõi một tín hiệu production gắn với bối cảnh: Consumer deploy độc lập và schema có thể đổi.
- Thử migration/rollback trên dữ liệu hoặc traffic đại diện trước rollout toàn phần.
- Review ADR khi kết quả đo không cải thiện lead time, incident risk hoặc khả năng cô lập thay đổi như kỳ vọng.

## Khi cần xem xét lại

Xem xét lại quyết định **ADR-032: Version Integration Event** khi tải thực tế, yêu cầu nhất quán, số biến thể triển khai hoặc ranh giới ownership thay đổi đủ lớn để làm các giả định ban đầu không còn đúng.

## Ghi chú triển khai

Quyết định này không tự động hợp thức hóa mọi trường hợp tương tự. Team triển khai phải ghi owner, phạm vi áp dụng, migration step và rollback trigger cho **Version Integration Event**. Review code cần kiểm tra implementation có giữ đúng boundary “Backward-compatible evolution; breaking change tạo version mới.” hay đã biến thành abstraction tổng quát ngoài phạm vi ADR.

## Decision drivers cụ thể

- Consumer độc lập, thời gian migration dài và nguy cơ breaking schema.
- Khả năng kiểm chứng bằng test và telemetry trước khi mở rộng phạm vi.
- Chi phí vận hành nếu phải duy trì hai đường xử lý trong thời gian migration.

## Rollout

Publish song song v1/v2, đo consumer adoption, chặn field removal cho đến khi v1 traffic về 0.

## Rollback

Duy trì publisher v1 trong cửa sổ rollback; không tái sử dụng event name cho semantics mới.
