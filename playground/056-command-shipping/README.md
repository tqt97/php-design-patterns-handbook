# Playground 56: Command — Shipping

## Mục tiêu học tập

Chạy một ví dụ nhỏ về **tính phí và ETA theo vùng** và quan sát cách **Command** giúp đóng gói intent thành object để queue/audit/retry. Playground này là drill 10–15 phút; flagship playground phù hợp hơn cho luồng nhiều bước.

Sau bài này, người học phải giải thích được **đóng gói intent thành object** trong bối cảnh shipping, chỉ ra dependency đã bị đảo hoặc che giấu, và nêu trường hợp giải pháp trực tiếp vẫn tốt hơn.

## Bối cảnh và invariant

- **Nghiệp vụ:** carrier, zone, weight và promised date.
- **Invariant:** phí và SLA phải nhất quán với tuyến giao hàng.
- **Change axis:** thêm audit/retry/queue quanh command.
- **Failure bắt buộc quan sát:** carrier timeout hoặc bảng giá không tồn tại; ở mức pattern cần chú ý thêm command bị chạy lặp hoặc thiếu authorization context.

```mermaid
sequenceDiagram
    participant U as Client
    participant B as CommandBus
    participant H as ShippingQuoteHandler
    participant P as CarrierAdapter
    U->>B: command + idempotency key
    B->>H: dispatch
    H->>P: execute side effect
    P-->>H: Shipping option
    H-->>B: result/event
    B-->>U: outcome
    Note over H,P: guard against Stale rate
```

## Cách chạy

```bash
php playground/056-command-shipping/index.php
```

Trước khi chạy, dự đoán output và ghi lại concrete detail mà client đang biết. Sau khi chạy, đối chiếu xem **tách caller khỏi handler và execution policy** đã làm client biết ít đi điều gì, đồng thời kiểm tra invariant của shipping vẫn được giữ.

## Thử nghiệm có hướng dẫn

1. Chạy baseline và lưu output/error hiện tại.
2. Thêm carrier mới và kiểm tra fallback không làm sai sla.
3. Tạo failure **carrier timeout hoặc bảng giá không tồn tại** và yêu cầu lỗi được biểu diễn rõ, không silent fallback.
4. Viết test tập trung vào **phí và SLA phải nhất quán với tuyến giao hàng**.
5. Thử bỏ abstraction; nếu code trực tiếp vẫn rõ và change axis chưa tồn tại, ghi lại lý do chưa áp dụng Command.

## Câu hỏi review

- Trong miền shipping, phần nào là policy/domain rule và phần nào chỉ là wiring?
- Command bảo vệ thay đổi **thêm audit/retry/queue quanh command** bằng cơ chế nào?
- Failure **carrier timeout hoặc bảng giá không tồn tại** được phát hiện, dịch và quan sát ở boundary nào?
- Abstraction có làm invariant **phí và SLA phải nhất quán với tuyến giao hàng** dễ test hơn không?
- Complexity mới xuất hiện ở đâu: số class, ordering, lifecycle hay debugging?

## Kết quả mong đợi

Một lời giải đạt yêu cầu phải chứng minh flow shipping vẫn giữ invariant khi thay implementation command, có assertion cho failure đã nêu và giải thích được trade-off của boundary mới. Không chấm điểm dựa trên số lượng interface hoặc class.
