# ADR-033: Feature flag cho refactor rủi ro

- **Trạng thái:** Accepted
- **Ngày:** 2026-08-01

## Bối cảnh

Cần rollout dần và rollback nhanh.

## Quyết định

Flag ở boundary use case, metric theo variant, có expiry owner.

## Các lựa chọn đã cân nhắc

1. **Không thay đổi:** giữ cách hiện tại, chấp nhận rủi ro được mô tả trong bối cảnh “Cần rollout dần và rollback nhanh.”.
2. **Giải pháp rộng hơn:** áp dụng abstraction/platform tổng quát cho nhiều use case, đổi lại tăng migration, ownership và chi phí vận hành.
3. **Quyết định của ADR:** Flag ở boundary use case, metric theo variant, có expiry owner.

Đối với **Feature flag cho refactor rủi ro**, lựa chọn thứ ba thắng vì giải quyết đúng failure/change axis hiện tại và có đường kiểm chứng cụ thể. Nếu assumption về tải, ownership hoặc consistency thay đổi, ADR phải được mở lại thay vì tiếp tục mở rộng abstraction theo quán tính.

## Hệ quả

Flag tạo nhánh tạm; phải cleanup.

## Cách kiểm chứng

- Viết test trực tiếp cho invariant hoặc compatibility rule mà **Feature flag cho refactor rủi ro** bảo vệ.
- Theo dõi một tín hiệu production gắn với bối cảnh: Cần rollout dần và rollback nhanh.
- Thử migration/rollback trên dữ liệu hoặc traffic đại diện trước rollout toàn phần.
- Review ADR khi kết quả đo không cải thiện lead time, incident risk hoặc khả năng cô lập thay đổi như kỳ vọng.

## Khi cần xem xét lại

Xem xét lại quyết định **ADR-033: Feature flag cho refactor rủi ro** khi tải thực tế, yêu cầu nhất quán, số biến thể triển khai hoặc ranh giới ownership thay đổi đủ lớn để làm các giả định ban đầu không còn đúng.

## Ghi chú triển khai

Quyết định này không tự động hợp thức hóa mọi trường hợp tương tự. Team triển khai phải ghi owner, phạm vi áp dụng, migration step và rollback trigger cho **Feature flag cho refactor rủi ro**. Review code cần kiểm tra implementation có giữ đúng boundary “Flag ở boundary use case, metric theo variant, có expiry owner.” hay đã biến thành abstraction tổng quát ngoài phạm vi ADR.

## Decision drivers cụ thể

- Blast radius, khả năng dual-run và chi phí giữ hai implementation.
- Khả năng kiểm chứng bằng test và telemetry trước khi mở rộng phạm vi.
- Chi phí vận hành nếu phải duy trì hai đường xử lý trong thời gian migration.

## Rollout

Internal user → small cohort → percentage rollout → full; mỗi bước có metric và thời gian quan sát.

## Rollback

Kill switch phải hoạt động không cần deploy; xóa flag sau khi cửa sổ rollback đóng.
