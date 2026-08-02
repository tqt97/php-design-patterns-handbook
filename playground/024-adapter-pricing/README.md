# Playground 24: Adapter — Pricing

## Mục tiêu học tập

Chạy một ví dụ nhỏ về **tính giá theo market và customer segment** và quan sát cách **Adapter** giúp dịch contract bên ngoài sang ngôn ngữ nội bộ. Playground này là drill 10–15 phút; flagship playground phù hợp hơn cho luồng nhiều bước.

Sau bài này, người học phải giải thích được **khác biệt contract ở integration boundary** trong bối cảnh pricing, chỉ ra dependency đã bị đảo hoặc che giấu, và nêu trường hợp giải pháp trực tiếp vẫn tốt hơn.

## Bối cảnh và invariant

- **Nghiệp vụ:** base price, discount, tax và rounding.
- **Invariant:** giá cuối cùng phải giải thích được và không âm.
- **Change axis:** thay SDK/vendor mà domain contract không đổi.
- **Failure bắt buộc quan sát:** rule conflict, currency mismatch hoặc rounding drift; ở mức pattern cần chú ý thêm mapping sai field, timeout hoặc lỗi vendor bị nuốt.

```mermaid
sequenceDiagram
    participant U as PricingRequest
    participant T as PricingPolicy
    participant A as RuleSet
    participant X as PriceBook
    U->>T: domain request
    T->>A: target contract
    A->>X: translate vendor request
    X-->>A: vendor response/error
    A-->>T: Final price or mapped failure
    T-->>U: stable result
```

## Cách chạy

```bash
php playground/024-adapter-pricing/index.php
```

Trước khi chạy, dự đoán output và ghi lại concrete detail mà client đang biết. Sau khi chạy, đối chiếu xem **dịch request, response và error semantics** đã làm client biết ít đi điều gì, đồng thời kiểm tra invariant của pricing vẫn được giữ.

## Thử nghiệm có hướng dẫn

1. Chạy baseline và lưu output/error hiện tại.
2. Thêm promotion chồng lấn và kiểm tra explanation breakdown.
3. Tạo failure **rule conflict, currency mismatch hoặc rounding drift** và yêu cầu lỗi được biểu diễn rõ, không silent fallback.
4. Viết test tập trung vào **giá cuối cùng phải giải thích được và không âm**.
5. Thử bỏ abstraction; nếu code trực tiếp vẫn rõ và change axis chưa tồn tại, ghi lại lý do chưa áp dụng Adapter.

## Câu hỏi review

- Trong miền pricing, phần nào là policy/domain rule và phần nào chỉ là wiring?
- Adapter bảo vệ thay đổi **thay SDK/vendor mà domain contract không đổi** bằng cơ chế nào?
- Failure **rule conflict, currency mismatch hoặc rounding drift** được phát hiện, dịch và quan sát ở boundary nào?
- Abstraction có làm invariant **giá cuối cùng phải giải thích được và không âm** dễ test hơn không?
- Complexity mới xuất hiện ở đâu: số class, ordering, lifecycle hay debugging?

## Kết quả mong đợi

Một lời giải đạt yêu cầu phải chứng minh flow pricing vẫn giữ invariant khi thay implementation adapter, có assertion cho failure đã nêu và giải thích được trade-off của boundary mới. Không chấm điểm dựa trên số lượng interface hoặc class.
