# Playground 39: Decorator — Notification

## Mục tiêu học tập

Chạy một ví dụ nhỏ về **gửi email/SMS/Chatwork theo preference** và quan sát cách **Decorator** giúp xếp lớp logging, retry, metrics quanh component. Playground này là drill 10–15 phút; flagship playground phù hợp hơn cho luồng nhiều bước.

Sau bài này, người học phải giải thích được **ghép behavior quanh cùng một contract** trong bối cảnh notification, chỉ ra dependency đã bị đảo hoặc che giấu, và nêu trường hợp giải pháp trực tiếp vẫn tốt hơn.

## Bối cảnh và invariant

- **Nghiệp vụ:** channel, template, recipient và delivery result.
- **Invariant:** mỗi notification có delivery identity và trạng thái rõ.
- **Change axis:** thay đổi composition/order của behavior.
- **Failure bắt buộc quan sát:** provider rate limit, duplicate delivery hoặc invalid recipient; ở mức pattern cần chú ý thêm wrapper order sai hoặc side effect bị thực thi hai lần.

```mermaid
flowchart LR
    A[NotificationRequest] --> V[Validation decorator]
    V --> L[Logging decorator]
    L --> RY[Retry decorator]
    RY --> C[ChannelAdapter]
    C --> O[Delivery receipt]
    RY -. stop retry .-> F[Duplicate delivery]
```

## Cách chạy

```bash
php playground/039-decorator-notification/index.php
```

Trước khi chạy, dự đoán output và ghi lại concrete detail mà client đang biết. Sau khi chạy, đối chiếu xem **wrapper ủy quyền và bổ sung trách nhiệm** đã làm client biết ít đi điều gì, đồng thời kiểm tra invariant của notification vẫn được giữ.

## Thử nghiệm có hướng dẫn

1. Chạy baseline và lưu output/error hiện tại.
2. Mô phỏng retry và chứng minh không gửi trùng.
3. Tạo failure **provider rate limit, duplicate delivery hoặc invalid recipient** và yêu cầu lỗi được biểu diễn rõ, không silent fallback.
4. Viết test tập trung vào **mỗi notification có delivery identity và trạng thái rõ**.
5. Thử bỏ abstraction; nếu code trực tiếp vẫn rõ và change axis chưa tồn tại, ghi lại lý do chưa áp dụng Decorator.

## Câu hỏi review

- Trong miền notification, phần nào là policy/domain rule và phần nào chỉ là wiring?
- Decorator bảo vệ thay đổi **thay đổi composition/order của behavior** bằng cơ chế nào?
- Failure **provider rate limit, duplicate delivery hoặc invalid recipient** được phát hiện, dịch và quan sát ở boundary nào?
- Abstraction có làm invariant **mỗi notification có delivery identity và trạng thái rõ** dễ test hơn không?
- Complexity mới xuất hiện ở đâu: số class, ordering, lifecycle hay debugging?

## Kết quả mong đợi

Một lời giải đạt yêu cầu phải chứng minh flow notification vẫn giữ invariant khi thay implementation decorator, có assertion cho failure đã nêu và giải thích được trade-off của boundary mới. Không chấm điểm dựa trên số lượng interface hoặc class.
