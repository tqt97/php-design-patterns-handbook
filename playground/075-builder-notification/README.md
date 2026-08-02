# Playground 75: Builder — Notification

## Mục tiêu học tập

Chạy một ví dụ nhỏ về **gửi email/SMS/Chatwork theo preference** và quan sát cách **Builder** giúp xây object phức tạp theo bước và kiểm tra invariant khi build. Playground này là drill 10–15 phút; flagship playground phù hợp hơn cho luồng nhiều bước.

Sau bài này, người học phải giải thích được **dựng object nhiều bước có invariant** trong bối cảnh notification, chỉ ra dependency đã bị đảo hoặc che giấu, và nêu trường hợp giải pháp trực tiếp vẫn tốt hơn.

## Bối cảnh và invariant

- **Nghiệp vụ:** channel, template, recipient và delivery result.
- **Invariant:** mỗi notification có delivery identity và trạng thái rõ.
- **Change axis:** thêm representation/step tùy chọn.
- **Failure bắt buộc quan sát:** provider rate limit, duplicate delivery hoặc invalid recipient; ở mức pattern cần chú ý thêm build object thiếu field bắt buộc hoặc state builder bị tái sử dụng sai.

```mermaid
flowchart LR
    A[NotificationRequest] --> B[Builder]
    B --> S1[Set required fields]
    S1 --> S2[Add optional configuration]
    S2 --> G{Validate invariant}
    G -->|valid| O[Delivery receipt]
    G -->|invalid| F[Duplicate delivery]
```

## Cách chạy

```bash
php playground/075-builder-notification/index.php
```

Trước khi chạy, dự đoán output và ghi lại concrete detail mà client đang biết. Sau khi chạy, đối chiếu xem **tách construction process khỏi representation** đã làm client biết ít đi điều gì, đồng thời kiểm tra invariant của notification vẫn được giữ.

## Thử nghiệm có hướng dẫn

1. Chạy baseline và lưu output/error hiện tại.
2. Mô phỏng retry và chứng minh không gửi trùng.
3. Tạo failure **provider rate limit, duplicate delivery hoặc invalid recipient** và yêu cầu lỗi được biểu diễn rõ, không silent fallback.
4. Viết test tập trung vào **mỗi notification có delivery identity và trạng thái rõ**.
5. Thử bỏ abstraction; nếu code trực tiếp vẫn rõ và change axis chưa tồn tại, ghi lại lý do chưa áp dụng Builder.

## Câu hỏi review

- Trong miền notification, phần nào là policy/domain rule và phần nào chỉ là wiring?
- Builder bảo vệ thay đổi **thêm representation/step tùy chọn** bằng cơ chế nào?
- Failure **provider rate limit, duplicate delivery hoặc invalid recipient** được phát hiện, dịch và quan sát ở boundary nào?
- Abstraction có làm invariant **mỗi notification có delivery identity và trạng thái rõ** dễ test hơn không?
- Complexity mới xuất hiện ở đâu: số class, ordering, lifecycle hay debugging?

## Kết quả mong đợi

Một lời giải đạt yêu cầu phải chứng minh flow notification vẫn giữ invariant khi thay implementation builder, có assertion cho failure đã nêu và giải thích được trade-off của boundary mới. Không chấm điểm dựa trên số lượng interface hoặc class.
