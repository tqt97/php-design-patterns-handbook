# ADR-029: Contract test cho adapter

- **Trạng thái:** Accepted
- **Ngày:** 2026-08-01

## Bối cảnh

Nhiều provider phải giữ semantics giống nhau.

## Quyết định

Suite contract test chạy cho từng adapter; integration test sandbox riêng.

## Các lựa chọn đã cân nhắc

1. **Không thay đổi:** giữ cách hiện tại, chấp nhận rủi ro được mô tả trong bối cảnh “Nhiều provider phải giữ semantics giống nhau.”.
2. **Giải pháp rộng hơn:** áp dụng abstraction/platform tổng quát cho nhiều use case, đổi lại tăng migration, ownership và chi phí vận hành.
3. **Quyết định của ADR:** Suite contract test chạy cho từng adapter; integration test sandbox riêng.

Đối với **Contract test cho adapter**, lựa chọn thứ ba thắng vì giải quyết đúng failure/change axis hiện tại và có đường kiểm chứng cụ thể. Nếu assumption về tải, ownership hoặc consistency thay đổi, ADR phải được mở lại thay vì tiếp tục mở rộng abstraction theo quán tính.

## Hệ quả

Test lâu hơn nhưng phát hiện semantic drift.

## Cách kiểm chứng

- Viết test trực tiếp cho invariant hoặc compatibility rule mà **Contract test cho adapter** bảo vệ.
- Theo dõi một tín hiệu production gắn với bối cảnh: Nhiều provider phải giữ semantics giống nhau.
- Thử migration/rollback trên dữ liệu hoặc traffic đại diện trước rollout toàn phần.
- Review ADR khi kết quả đo không cải thiện lead time, incident risk hoặc khả năng cô lập thay đổi như kỳ vọng.

## Khi cần xem xét lại

Xem xét lại quyết định **ADR-029: Contract test cho adapter** khi tải thực tế, yêu cầu nhất quán, số biến thể triển khai hoặc ranh giới ownership thay đổi đủ lớn để làm các giả định ban đầu không còn đúng.

## Ghi chú triển khai

Quyết định này không tự động hợp thức hóa mọi trường hợp tương tự. Team triển khai phải ghi owner, phạm vi áp dụng, migration step và rollback trigger cho **Contract test cho adapter**. Review code cần kiểm tra implementation có giữ đúng boundary “Suite contract test chạy cho từng adapter; integration test sandbox riêng.” hay đã biến thành abstraction tổng quát ngoài phạm vi ADR.

## Decision drivers cụ thể

- Vendor drift, mapping error và stable error contract của application.
- Khả năng kiểm chứng bằng test và telemetry trước khi mở rộng phạm vi.
- Chi phí vận hành nếu phải duy trì hai đường xử lý trong thời gian migration.

## Rollout

Chạy contract suite với sandbox/recorded fixtures trước mỗi adapter release và theo lịch.

## Rollback

Pin adapter version hoặc chuyển provider fallback khi contract test thất bại.
