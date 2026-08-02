# Playground 94: Proxy — Export

## Mục tiêu học tập

Quan sát Proxy trong miền export.

Sau bài này, người học phải giải thích được **kiểm soát truy cập vào object thật** trong bối cảnh export, chỉ ra dependency đã bị đảo hoặc che giấu, và nêu trường hợp giải pháp trực tiếp vẫn tốt hơn.

## Bối cảnh và invariant

- **Nghiệp vụ:** format, schema version, stream và file name.
- **Invariant:** cùng dataset phải giữ schema và encoding đã cam kết.
- **Change axis:** thêm access policy mà giữ interface.
- **Failure bắt buộc quan sát:** writer lỗi giữa chừng hoặc dữ liệu không encode được; ở mức pattern cần chú ý thêm proxy thay đổi semantics, cache stale hoặc bypass authorization.

```mermaid
sequenceDiagram
    participant U as Client
    participant P as Proxy
    participant S as FormatWriter
    U->>P: ExportRequest
    P->>P: authorize/cache/rate-limit
    alt allowed
      P->>S: delegate
      S-->>P: Export artifact
      P-->>U: result
    else blocked
      P-->>U: explicit Partial file
    end
```

## Cách chạy

```bash
php playground/094-proxy-export/index.php
```

Trước khi chạy, dự đoán output và ghi lại concrete detail mà client đang biết. Sau khi chạy, đối chiếu xem **intercept authorization, cache hoặc lazy loading** đã làm client biết ít đi điều gì, đồng thời kiểm tra invariant của export vẫn được giữ.

## Thử nghiệm có hướng dẫn

1. Chạy baseline và lưu output/error hiện tại.
2. Thêm format mới và kiểm tra checksum/schema.
3. Tạo failure **writer lỗi giữa chừng hoặc dữ liệu không encode được** và yêu cầu lỗi được biểu diễn rõ, không silent fallback.
4. Viết test tập trung vào **cùng dataset phải giữ schema và encoding đã cam kết**.
5. Thử bỏ abstraction; nếu code trực tiếp vẫn rõ và change axis chưa tồn tại, ghi lại lý do chưa áp dụng Proxy.

## Câu hỏi review

- Trong miền export, phần nào là policy/domain rule và phần nào chỉ là wiring?
- Proxy bảo vệ thay đổi **thêm access policy mà giữ interface** bằng cơ chế nào?
- Failure **writer lỗi giữa chừng hoặc dữ liệu không encode được** được phát hiện, dịch và quan sát ở boundary nào?
- Abstraction có làm invariant **cùng dataset phải giữ schema và encoding đã cam kết** dễ test hơn không?
- Complexity mới xuất hiện ở đâu: số class, ordering, lifecycle hay debugging?

## Kết quả mong đợi

Một lời giải đạt yêu cầu phải chứng minh flow export vẫn giữ invariant khi thay implementation proxy, có assertion cho failure đã nêu và giải thích được trade-off của boundary mới. Không chấm điểm dựa trên số lượng interface hoặc class.
