# Playground 87: Facade — Pricing

## Mục tiêu học tập

Chạy một ví dụ nhỏ về **tính giá theo market và customer segment** và quan sát cách **Facade** giúp cung cấp API use-case đơn giản trước nhiều subsystem. Playground này là drill 10–15 phút; flagship playground phù hợp hơn cho luồng nhiều bước.

Sau bài này, người học phải giải thích được **đơn giản hóa orchestration nhiều subsystem** trong bối cảnh pricing, chỉ ra dependency đã bị đảo hoặc che giấu, và nêu trường hợp giải pháp trực tiếp vẫn tốt hơn.

## Bối cảnh và invariant

- **Nghiệp vụ:** base price, discount, tax và rounding.
- **Invariant:** giá cuối cùng phải giải thích được và không âm.
- **Change axis:** thay subsystem mà client không đổi.
- **Failure bắt buộc quan sát:** rule conflict, currency mismatch hoặc rounding drift; ở mức pattern cần chú ý thêm facade trở thành god service hoặc che giấu transaction lỗi.

```mermaid
flowchart LR
    A[PricingRequest] --> F[Facade]
    F --> S1[PricingPolicy]
    F --> S2[RuleSet]
    F --> S3[PriceBook]
    S1 --> R[Final price]
    S2 --> R
    S3 -. failure .-> X[Invalid discount]
```

## Cách chạy

```bash
php playground/087-facade-pricing/index.php
```

Trước khi chạy, dự đoán output và ghi lại concrete detail mà client đang biết. Sau khi chạy, đối chiếu xem **cung cấp entry point theo use case** đã làm client biết ít đi điều gì, đồng thời kiểm tra invariant của pricing vẫn được giữ.

## Thử nghiệm có hướng dẫn

1. Chạy baseline và lưu output/error hiện tại.
2. Thêm promotion chồng lấn và kiểm tra explanation breakdown.
3. Tạo failure **rule conflict, currency mismatch hoặc rounding drift** và yêu cầu lỗi được biểu diễn rõ, không silent fallback.
4. Viết test tập trung vào **giá cuối cùng phải giải thích được và không âm**.
5. Thử bỏ abstraction; nếu code trực tiếp vẫn rõ và change axis chưa tồn tại, ghi lại lý do chưa áp dụng Facade.

## Câu hỏi review

- Trong miền pricing, phần nào là policy/domain rule và phần nào chỉ là wiring?
- Facade bảo vệ thay đổi **thay subsystem mà client không đổi** bằng cơ chế nào?
- Failure **rule conflict, currency mismatch hoặc rounding drift** được phát hiện, dịch và quan sát ở boundary nào?
- Abstraction có làm invariant **giá cuối cùng phải giải thích được và không âm** dễ test hơn không?
- Complexity mới xuất hiện ở đâu: số class, ordering, lifecycle hay debugging?

## Kết quả mong đợi

Một lời giải đạt yêu cầu phải chứng minh flow pricing vẫn giữ invariant khi thay implementation facade, có assertion cho failure đã nêu và giải thích được trade-off của boundary mới. Không chấm điểm dựa trên số lượng interface hoặc class.
