# Playground 57: Command — Notification

## Mục tiêu học tập

Chạy một ví dụ nhỏ về **gửi email/SMS/Chatwork theo preference** và quan sát cách **Command** giúp đóng gói intent thành object để queue/audit/retry. Playground này là drill 10–15 phút; flagship playground phù hợp hơn cho luồng nhiều bước.

Sau bài này, người học phải giải thích được **đóng gói intent thành object** trong bối cảnh notification, chỉ ra dependency đã bị đảo hoặc che giấu, và nêu trường hợp giải pháp trực tiếp vẫn tốt hơn.

## Bối cảnh và invariant

- **Nghiệp vụ:** channel, template, recipient và delivery result.
- **Invariant:** mỗi notification có delivery identity và trạng thái rõ.
- **Change axis:** thêm audit/retry/queue quanh command.
- **Failure bắt buộc quan sát:** provider rate limit, duplicate delivery hoặc invalid recipient; ở mức pattern cần chú ý thêm command bị chạy lặp hoặc thiếu authorization context.

```mermaid
sequenceDiagram
    participant U as Client
    participant B as CommandBus
    participant H as NotificationRequestHandler
    participant P as ChannelAdapter
    U->>B: command + idempotency key
    B->>H: dispatch
    H->>P: execute side effect
    P-->>H: Delivery receipt
    H-->>B: result/event
    B-->>U: outcome
    Note over H,P: guard against Duplicate delivery
```

## Cách chạy

```bash
php playground/057-command-notification/index.php
```

Trước khi chạy, dự đoán output và ghi lại concrete detail mà client đang biết. Sau khi chạy, đối chiếu xem **tách caller khỏi handler và execution policy** đã làm client biết ít đi điều gì, đồng thời kiểm tra invariant của notification vẫn được giữ.

## Thử nghiệm có hướng dẫn

1. Chạy baseline và lưu output/error hiện tại.
2. Mô phỏng retry và chứng minh không gửi trùng.
3. Tạo failure **provider rate limit, duplicate delivery hoặc invalid recipient** và yêu cầu lỗi được biểu diễn rõ, không silent fallback.
4. Viết test tập trung vào **mỗi notification có delivery identity và trạng thái rõ**.
5. Thử bỏ abstraction; nếu code trực tiếp vẫn rõ và change axis chưa tồn tại, ghi lại lý do chưa áp dụng Command.

## Câu hỏi review

- Trong miền notification, phần nào là policy/domain rule và phần nào chỉ là wiring?
- Command bảo vệ thay đổi **thêm audit/retry/queue quanh command** bằng cơ chế nào?
- Failure **provider rate limit, duplicate delivery hoặc invalid recipient** được phát hiện, dịch và quan sát ở boundary nào?
- Abstraction có làm invariant **mỗi notification có delivery identity và trạng thái rõ** dễ test hơn không?
- Complexity mới xuất hiện ở đâu: số class, ordering, lifecycle hay debugging?

## Kết quả mong đợi

Một lời giải đạt yêu cầu phải chứng minh flow notification vẫn giữ invariant khi thay implementation command, có assertion cho failure đã nêu và giải thích được trade-off của boundary mới. Không chấm điểm dựa trên số lượng interface hoặc class.
