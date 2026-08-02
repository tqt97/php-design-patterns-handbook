# Playground 22: Adapter — Export

## Mục tiêu học tập

Chạy một ví dụ nhỏ về **xử lý use case export** và quan sát cách **Adapter** giúp dịch contract bên ngoài sang ngôn ngữ nội bộ. Playground này là drill 10–15 phút; flagship playground phù hợp hơn cho luồng nhiều bước.

Sau bài này, người học phải giải thích được **khác biệt contract ở integration boundary** trong bối cảnh export, chỉ ra dependency đã bị đảo hoặc che giấu, và nêu trường hợp giải pháp trực tiếp vẫn tốt hơn.

## Bối cảnh và invariant

- **Nghiệp vụ:** format, schema version, stream và file name.
- **Invariant:** cùng dataset phải giữ schema và encoding đã cam kết.
- **Change axis:** thay SDK/vendor mà domain contract không đổi.
- **Failure bắt buộc quan sát:** writer lỗi giữa chừng hoặc dữ liệu không encode được; ở mức pattern cần chú ý thêm mapping sai field, timeout hoặc lỗi vendor bị nuốt.

```mermaid
sequenceDiagram
    participant U as ExportRequest
    participant T as Exporter
    participant A as FormatWriter
    participant X as Storage
    U->>T: domain request
    T->>A: target contract
    A->>X: translate vendor request
    X-->>A: vendor response/error
    A-->>T: Export artifact or mapped failure
    T-->>U: stable result
```

## Cách chạy

```bash
php playground/022-adapter-export/index.php
```

Trước khi chạy, dự đoán output và ghi lại concrete detail mà client đang biết. Sau khi chạy, đối chiếu xem **dịch request, response và error semantics** đã làm client biết ít đi điều gì, đồng thời kiểm tra invariant của export vẫn được giữ.

## Thử nghiệm có hướng dẫn

1. Chạy baseline và lưu output/error hiện tại.
2. Thêm format mới và kiểm tra checksum/schema.
3. Tạo failure **writer lỗi giữa chừng hoặc dữ liệu không encode được** và yêu cầu lỗi được biểu diễn rõ, không silent fallback.
4. Viết test tập trung vào **cùng dataset phải giữ schema và encoding đã cam kết**.
5. Thử bỏ abstraction; nếu code trực tiếp vẫn rõ và change axis chưa tồn tại, ghi lại lý do chưa áp dụng Adapter.

## Câu hỏi review

- Trong miền export, phần nào là policy/domain rule và phần nào chỉ là wiring?
- Adapter bảo vệ thay đổi **thay SDK/vendor mà domain contract không đổi** bằng cơ chế nào?
- Failure **writer lỗi giữa chừng hoặc dữ liệu không encode được** được phát hiện, dịch và quan sát ở boundary nào?
- Abstraction có làm invariant **cùng dataset phải giữ schema và encoding đã cam kết** dễ test hơn không?
- Complexity mới xuất hiện ở đâu: số class, ordering, lifecycle hay debugging?

## Kết quả mong đợi

Một lời giải đạt yêu cầu phải chứng minh flow export vẫn giữ invariant khi thay implementation adapter, có assertion cho failure đã nêu và giải thích được trade-off của boundary mới. Không chấm điểm dựa trên số lượng interface hoặc class.
