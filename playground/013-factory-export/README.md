# Playground 13: Factory — Export

## Mục tiêu học tập

Quan sát Factory trong miền export.

Sau bài này, người học phải giải thích được **quyền sở hữu việc tạo object** trong bối cảnh export, chỉ ra dependency đã bị đảo hoặc che giấu, và nêu trường hợp giải pháp trực tiếp vẫn tốt hơn.

## Bối cảnh và invariant

- **Nghiệp vụ:** format, schema version, stream và file name.
- **Invariant:** cùng dataset phải giữ schema và encoding đã cam kết.
- **Change axis:** thêm product mới qua creator/factory boundary.
- **Failure bắt buộc quan sát:** writer lỗi giữa chừng hoặc dữ liệu không encode được; ở mức pattern cần chú ý thêm factory trả object sai capability hoặc cấu hình thiếu.

```mermaid
flowchart LR
    A[ExportRequest] --> CR[Creator]
    CR -->|factory method| P[FormatWriter]
    P --> V1[Implementation A]
    P --> V2[Implementation B]
    V1 --> R[Export artifact]
    V2 --> R
```

## Cách chạy

```bash
php playground/013-factory-export/index.php
```

Trước khi chạy, dự đoán output và ghi lại concrete detail mà client đang biết. Sau khi chạy, đối chiếu xem **ẩn concrete product và construction rule** đã làm client biết ít đi điều gì, đồng thời kiểm tra invariant của export vẫn được giữ.

## Thử nghiệm có hướng dẫn

1. Chạy baseline và lưu output/error hiện tại.
2. Thêm format mới và kiểm tra checksum/schema.
3. Tạo failure **writer lỗi giữa chừng hoặc dữ liệu không encode được** và yêu cầu lỗi được biểu diễn rõ, không silent fallback.
4. Viết test tập trung vào **cùng dataset phải giữ schema và encoding đã cam kết**.
5. Thử bỏ abstraction; nếu code trực tiếp vẫn rõ và change axis chưa tồn tại, ghi lại lý do chưa áp dụng Factory.

## Câu hỏi review

- Trong miền export, phần nào là policy/domain rule và phần nào chỉ là wiring?
- Factory bảo vệ thay đổi **thêm product mới qua creator/factory boundary** bằng cơ chế nào?
- Failure **writer lỗi giữa chừng hoặc dữ liệu không encode được** được phát hiện, dịch và quan sát ở boundary nào?
- Abstraction có làm invariant **cùng dataset phải giữ schema và encoding đã cam kết** dễ test hơn không?
- Complexity mới xuất hiện ở đâu: số class, ordering, lifecycle hay debugging?

## Kết quả mong đợi

Một lời giải đạt yêu cầu phải chứng minh flow export vẫn giữ invariant khi thay implementation factory, có assertion cho failure đã nêu và giải thích được trade-off của boundary mới. Không chấm điểm dựa trên số lượng interface hoặc class.
