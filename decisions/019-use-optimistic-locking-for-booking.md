# ADR-019: Optimistic locking cho booking

- **Trạng thái:** Accepted
- **Ngày:** 2026-08-01

## Bối cảnh

Hai request có thể cập nhật cùng reservation.

## Quyết định

Version column và conflict retry có giới hạn.

## Các lựa chọn đã cân nhắc

1. **Không thay đổi:** giữ cách hiện tại, chấp nhận rủi ro được mô tả trong bối cảnh “Hai request có thể cập nhật cùng reservation.”.
2. **Giải pháp rộng hơn:** áp dụng abstraction/platform tổng quát cho nhiều use case, đổi lại tăng migration, ownership và chi phí vận hành.
3. **Quyết định của ADR:** Version column và conflict retry có giới hạn.

Đối với **Optimistic locking cho booking**, lựa chọn thứ ba thắng vì giải quyết đúng failure/change axis hiện tại và có đường kiểm chứng cụ thể. Nếu assumption về tải, ownership hoặc consistency thay đổi, ADR phải được mở lại thay vì tiếp tục mở rộng abstraction theo quán tính.

## Hệ quả

Người dùng có thể phải retry; cần UX conflict.

## Cách kiểm chứng

- Viết test trực tiếp cho invariant hoặc compatibility rule mà **Optimistic locking cho booking** bảo vệ.
- Theo dõi một tín hiệu production gắn với bối cảnh: Hai request có thể cập nhật cùng reservation.
- Thử migration/rollback trên dữ liệu hoặc traffic đại diện trước rollout toàn phần.
- Review ADR khi kết quả đo không cải thiện lead time, incident risk hoặc khả năng cô lập thay đổi như kỳ vọng.

## Khi cần xem xét lại

Xem xét lại quyết định **ADR-019: Optimistic locking cho booking** khi tải thực tế, yêu cầu nhất quán, số biến thể triển khai hoặc ranh giới ownership thay đổi đủ lớn để làm các giả định ban đầu không còn đúng.

## Ghi chú triển khai

Quyết định này không tự động hợp thức hóa mọi trường hợp tương tự. Team triển khai phải ghi owner, phạm vi áp dụng, migration step và rollback trigger cho **Optimistic locking cho booking**. Review code cần kiểm tra implementation có giữ đúng boundary “Version column và conflict retry có giới hạn.” hay đã biến thành abstraction tổng quát ngoài phạm vi ADR.

## Decision drivers cụ thể

- Conflict rate, thời gian giữ booking và UX khi version stale.
- Khả năng kiểm chứng bằng test và telemetry trước khi mở rộng phạm vi.
- Chi phí vận hành nếu phải duy trì hai đường xử lý trong thời gian migration.

## Rollout

Thêm version nullable, backfill, bật conditional update theo feature flag, theo dõi conflict, rồi bắt buộc version.

## Rollback

Rollback về update cũ chỉ khi chưa có client phụ thuộc conflict response; giữ cột version để tránh migration ngược rủi ro.
