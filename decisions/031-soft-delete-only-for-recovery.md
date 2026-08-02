# ADR-031: Chỉ soft delete khi có recovery/audit requirement

- **Trạng thái:** Accepted
- **Ngày:** 2026-08-01

## Bối cảnh

Soft delete mặc định làm mọi query phức tạp.

## Quyết định

Dùng khi business cần restore/retention; còn lại hard delete có kiểm soát.

## Các lựa chọn đã cân nhắc

1. **Không thay đổi:** giữ cách hiện tại, chấp nhận rủi ro được mô tả trong bối cảnh “Soft delete mặc định làm mọi query phức tạp.”.
2. **Giải pháp rộng hơn:** áp dụng abstraction/platform tổng quát cho nhiều use case, đổi lại tăng migration, ownership và chi phí vận hành.
3. **Quyết định của ADR:** Dùng khi business cần restore/retention; còn lại hard delete có kiểm soát.

Đối với **Chỉ soft delete khi có recovery/audit requirement**, lựa chọn thứ ba thắng vì giải quyết đúng failure/change axis hiện tại và có đường kiểm chứng cụ thể. Nếu assumption về tải, ownership hoặc consistency thay đổi, ADR phải được mở lại thay vì tiếp tục mở rộng abstraction theo quán tính.

## Hệ quả

Recovery không miễn phí; cần purge policy.

## Cách kiểm chứng

- Viết test trực tiếp cho invariant hoặc compatibility rule mà **Chỉ soft delete khi có recovery/audit requirement** bảo vệ.
- Theo dõi một tín hiệu production gắn với bối cảnh: Soft delete mặc định làm mọi query phức tạp.
- Thử migration/rollback trên dữ liệu hoặc traffic đại diện trước rollout toàn phần.
- Review ADR khi kết quả đo không cải thiện lead time, incident risk hoặc khả năng cô lập thay đổi như kỳ vọng.

## Khi cần xem xét lại

Xem xét lại quyết định **ADR-031: Chỉ soft delete khi có recovery/audit requirement** khi tải thực tế, yêu cầu nhất quán, số biến thể triển khai hoặc ranh giới ownership thay đổi đủ lớn để làm các giả định ban đầu không còn đúng.

## Ghi chú triển khai

Quyết định này không tự động hợp thức hóa mọi trường hợp tương tự. Team triển khai phải ghi owner, phạm vi áp dụng, migration step và rollback trigger cho **Chỉ soft delete khi có recovery/audit requirement**. Review code cần kiểm tra implementation có giữ đúng boundary “Dùng khi business cần restore/retention; còn lại hard delete có kiểm soát.” hay đã biến thành abstraction tổng quát ngoài phạm vi ADR.
