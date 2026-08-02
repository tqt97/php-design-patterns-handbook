# Playground 88: Facade — Reporting

## Mục tiêu học tập

Chạy một ví dụ nhỏ về **xử lý use case reporting** và quan sát cách **Facade** giúp cung cấp API use-case đơn giản trước nhiều subsystem. Playground này là drill 10–15 phút; flagship playground phù hợp hơn cho luồng nhiều bước.

Sau bài này, người học phải giải thích được **đơn giản hóa orchestration nhiều subsystem** trong bối cảnh reporting, chỉ ra dependency đã bị đảo hoặc che giấu, và nêu trường hợp giải pháp trực tiếp vẫn tốt hơn.

## Bối cảnh và invariant

- **Nghiệp vụ:** criteria, projection, pagination và aggregation.
- **Invariant:** report phải tái lập từ cùng input và timezone.
- **Change axis:** thay subsystem mà client không đổi.
- **Failure bắt buộc quan sát:** query timeout, missing column hoặc cursor invalid; ở mức pattern cần chú ý thêm facade trở thành god service hoặc che giấu transaction lỗi.

```mermaid
flowchart LR
    A[ReportQuery] --> F[Facade]
    F --> S1[ReportService]
    F --> S2[ReadModel]
    F --> S3[Data source]
    S1 --> R[Report view]
    S2 --> R
    S3 -. failure .-> X[Stale projection]
```

## Cách chạy

```bash
php playground/088-facade-reporting/index.php
```

Trước khi chạy, dự đoán output và ghi lại concrete detail mà client đang biết. Sau khi chạy, đối chiếu xem **cung cấp entry point theo use case** đã làm client biết ít đi điều gì, đồng thời kiểm tra invariant của reporting vẫn được giữ.

## Thử nghiệm có hướng dẫn

1. Chạy baseline và lưu output/error hiện tại.
2. Thêm cột/projection và kiểm tra backward compatibility.
3. Tạo failure **query timeout, missing column hoặc cursor invalid** và yêu cầu lỗi được biểu diễn rõ, không silent fallback.
4. Viết test tập trung vào **report phải tái lập từ cùng input và timezone**.
5. Thử bỏ abstraction; nếu code trực tiếp vẫn rõ và change axis chưa tồn tại, ghi lại lý do chưa áp dụng Facade.

## Câu hỏi review

- Trong miền reporting, phần nào là policy/domain rule và phần nào chỉ là wiring?
- Facade bảo vệ thay đổi **thay subsystem mà client không đổi** bằng cơ chế nào?
- Failure **query timeout, missing column hoặc cursor invalid** được phát hiện, dịch và quan sát ở boundary nào?
- Abstraction có làm invariant **report phải tái lập từ cùng input và timezone** dễ test hơn không?
- Complexity mới xuất hiện ở đâu: số class, ordering, lifecycle hay debugging?

## Kết quả mong đợi

Một lời giải đạt yêu cầu phải chứng minh flow reporting vẫn giữ invariant khi thay implementation facade, có assertion cho failure đã nêu và giải thích được trade-off của boundary mới. Không chấm điểm dựa trên số lượng interface hoặc class.
