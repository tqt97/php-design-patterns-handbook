# ADR-023: Không dùng Event Sourcing mặc định

- **Trạng thái:** Accepted
- **Ngày:** 2026-08-01

## Bối cảnh

Audit requirement chưa đồng nghĩa cần rebuild state từ event.

## Quyết định

Dùng audit log/outbox trước; chỉ chọn ES khi temporal model là cốt lõi.

## Các lựa chọn đã cân nhắc

1. **Không thay đổi:** giữ cách hiện tại, chấp nhận rủi ro được mô tả trong bối cảnh “Audit requirement chưa đồng nghĩa cần rebuild state từ event.”.
2. **Giải pháp rộng hơn:** áp dụng abstraction/platform tổng quát cho nhiều use case, đổi lại tăng migration, ownership và chi phí vận hành.
3. **Quyết định của ADR:** Dùng audit log/outbox trước; chỉ chọn ES khi temporal model là cốt lõi.

Đối với **Không dùng Event Sourcing mặc định**, lựa chọn thứ ba thắng vì giải quyết đúng failure/change axis hiện tại và có đường kiểm chứng cụ thể. Nếu assumption về tải, ownership hoặc consistency thay đổi, ADR phải được mở lại thay vì tiếp tục mở rộng abstraction theo quán tính.

## Hệ quả

Bỏ qua khả năng replay đầy đủ để giảm complexity.

## Cách kiểm chứng

- Viết test trực tiếp cho invariant hoặc compatibility rule mà **Không dùng Event Sourcing mặc định** bảo vệ.
- Theo dõi một tín hiệu production gắn với bối cảnh: Audit requirement chưa đồng nghĩa cần rebuild state từ event.
- Thử migration/rollback trên dữ liệu hoặc traffic đại diện trước rollout toàn phần.
- Review ADR khi kết quả đo không cải thiện lead time, incident risk hoặc khả năng cô lập thay đổi như kỳ vọng.

## Khi cần xem xét lại

Xem xét lại quyết định **ADR-023: Không dùng Event Sourcing mặc định** khi tải thực tế, yêu cầu nhất quán, số biến thể triển khai hoặc ranh giới ownership thay đổi đủ lớn để làm các giả định ban đầu không còn đúng.

## Ghi chú triển khai

Quyết định này không tự động hợp thức hóa mọi trường hợp tương tự. Team triển khai phải ghi owner, phạm vi áp dụng, migration step và rollback trigger cho **Không dùng Event Sourcing mặc định**. Review code cần kiểm tra implementation có giữ đúng boundary “Dùng audit log/outbox trước; chỉ chọn ES khi temporal model là cốt lõi.” hay đã biến thành abstraction tổng quát ngoài phạm vi ADR.
