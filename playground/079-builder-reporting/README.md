# Playground 79: Builder — Reporting

## Mục tiêu học tập

Chạy một ví dụ nhỏ về **xử lý use case reporting** và quan sát cách **Builder** giúp xây object phức tạp theo bước và kiểm tra invariant khi build. Playground này là drill 10–15 phút; flagship playground phù hợp hơn cho luồng nhiều bước.

Sau bài này, người học phải giải thích được **dựng object nhiều bước có invariant** trong bối cảnh reporting, chỉ ra dependency đã bị đảo hoặc che giấu, và nêu trường hợp giải pháp trực tiếp vẫn tốt hơn.

## Bối cảnh và invariant

- **Nghiệp vụ:** criteria, projection, pagination và aggregation.
- **Invariant:** report phải tái lập từ cùng input và timezone.
- **Change axis:** thêm representation/step tùy chọn.
- **Failure bắt buộc quan sát:** query timeout, missing column hoặc cursor invalid; ở mức pattern cần chú ý thêm build object thiếu field bắt buộc hoặc state builder bị tái sử dụng sai.

```mermaid
flowchart LR
    A[ReportQuery] --> B[Builder]
    B --> S1[Set required fields]
    S1 --> S2[Add optional configuration]
    S2 --> G{Validate invariant}
    G -->|valid| O[Report view]
    G -->|invalid| F[Stale projection]
```

## Cách chạy

```bash
php playground/079-builder-reporting/index.php
```

Trước khi chạy, dự đoán output và ghi lại concrete detail mà client đang biết. Sau khi chạy, đối chiếu xem **tách construction process khỏi representation** đã làm client biết ít đi điều gì, đồng thời kiểm tra invariant của reporting vẫn được giữ.

## Thử nghiệm có hướng dẫn

1. Chạy baseline và lưu output/error hiện tại.
2. Thêm cột/projection và kiểm tra backward compatibility.
3. Tạo failure **query timeout, missing column hoặc cursor invalid** và yêu cầu lỗi được biểu diễn rõ, không silent fallback.
4. Viết test tập trung vào **report phải tái lập từ cùng input và timezone**.
5. Thử bỏ abstraction; nếu code trực tiếp vẫn rõ và change axis chưa tồn tại, ghi lại lý do chưa áp dụng Builder.

## Câu hỏi review

- Trong miền reporting, phần nào là policy/domain rule và phần nào chỉ là wiring?
- Builder bảo vệ thay đổi **thêm representation/step tùy chọn** bằng cơ chế nào?
- Failure **query timeout, missing column hoặc cursor invalid** được phát hiện, dịch và quan sát ở boundary nào?
- Abstraction có làm invariant **report phải tái lập từ cùng input và timezone** dễ test hơn không?
- Complexity mới xuất hiện ở đâu: số class, ordering, lifecycle hay debugging?

## Kết quả mong đợi

Một lời giải đạt yêu cầu phải chứng minh flow reporting vẫn giữ invariant khi thay implementation builder, có assertion cho failure đã nêu và giải thích được trade-off của boundary mới. Không chấm điểm dựa trên số lượng interface hoặc class.
