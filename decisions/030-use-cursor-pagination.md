# ADR-030: Cursor pagination cho activity feed

- **Trạng thái:** Accepted
- **Ngày:** 2026-08-01

## Bối cảnh

Offset chậm và duplicate khi dữ liệu thay đổi.

## Quyết định

Cursor dựa trên `(created_at,id)` ổn định.

## Các lựa chọn đã cân nhắc

1. **Không thay đổi:** giữ cách hiện tại, chấp nhận rủi ro được mô tả trong bối cảnh “Offset chậm và duplicate khi dữ liệu thay đổi.”.
2. **Giải pháp rộng hơn:** áp dụng abstraction/platform tổng quát cho nhiều use case, đổi lại tăng migration, ownership và chi phí vận hành.
3. **Quyết định của ADR:** Cursor dựa trên `(created_at,id)` ổn định.

Đối với **Cursor pagination cho activity feed**, lựa chọn thứ ba thắng vì giải quyết đúng failure/change axis hiện tại và có đường kiểm chứng cụ thể. Nếu assumption về tải, ownership hoặc consistency thay đổi, ADR phải được mở lại thay vì tiếp tục mở rộng abstraction theo quán tính.

## Hệ quả

Không nhảy trực tiếp đến trang N.

## Cách kiểm chứng

- Viết test trực tiếp cho invariant hoặc compatibility rule mà **Cursor pagination cho activity feed** bảo vệ.
- Theo dõi một tín hiệu production gắn với bối cảnh: Offset chậm và duplicate khi dữ liệu thay đổi.
- Thử migration/rollback trên dữ liệu hoặc traffic đại diện trước rollout toàn phần.
- Review ADR khi kết quả đo không cải thiện lead time, incident risk hoặc khả năng cô lập thay đổi như kỳ vọng.

## Khi cần xem xét lại

Xem xét lại quyết định **ADR-030: Cursor pagination cho activity feed** khi tải thực tế, yêu cầu nhất quán, số biến thể triển khai hoặc ranh giới ownership thay đổi đủ lớn để làm các giả định ban đầu không còn đúng.

## Ghi chú triển khai

Quyết định này không tự động hợp thức hóa mọi trường hợp tương tự. Team triển khai phải ghi owner, phạm vi áp dụng, migration step và rollback trigger cho **Cursor pagination cho activity feed**. Review code cần kiểm tra implementation có giữ đúng boundary “Cursor dựa trên `(created_at,id)` ổn định.” hay đã biến thành abstraction tổng quát ngoài phạm vi ADR.

## Decision drivers cụ thể

- Dataset lớn, sort ổn định và yêu cầu tránh duplicate/missing row khi dữ liệu thay đổi.
- Khả năng kiểm chứng bằng test và telemetry trước khi mở rộng phạm vi.
- Chi phí vận hành nếu phải duy trì hai đường xử lý trong thời gian migration.

## Rollout

Thêm endpoint cursor song song offset, đối chiếu page boundary, migrate client theo cohort.

## Rollback

Giữ offset endpoint đến khi telemetry xác nhận client migration; cursor token phải versioned.
