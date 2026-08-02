# Playground 14: Factory — Authentication

## Mục tiêu học tập

Quan sát Factory trong miền authentication.

Sau bài này, người học phải giải thích được **quyền sở hữu việc tạo object** trong bối cảnh authentication, chỉ ra dependency đã bị đảo hoặc che giấu, và nêu trường hợp giải pháp trực tiếp vẫn tốt hơn.

## Bối cảnh và invariant

- **Nghiệp vụ:** credential verifier, identity và auth result.
- **Invariant:** credential không được log và quyết định auth phải nhất quán.
- **Change axis:** thêm product mới qua creator/factory boundary.
- **Failure bắt buộc quan sát:** provider unavailable, expired credential hoặc account locked; ở mức pattern cần chú ý thêm factory trả object sai capability hoặc cấu hình thiếu.

```mermaid
flowchart LR
    A[LoginRequest] --> CR[Creator]
    CR -->|factory method| P[IdentityAdapter]
    P --> V1[Implementation A]
    P --> V2[Implementation B]
    V1 --> R[Auth result]
    V2 --> R
```

## Cách chạy

```bash
php playground/014-factory-authentication/index.php
```

Trước khi chạy, dự đoán output và ghi lại concrete detail mà client đang biết. Sau khi chạy, đối chiếu xem **ẩn concrete product và construction rule** đã làm client biết ít đi điều gì, đồng thời kiểm tra invariant của authentication vẫn được giữ.

## Thử nghiệm có hướng dẫn

1. Chạy baseline và lưu output/error hiện tại.
2. Thử account locked và bảo đảm lỗi không rò thông tin nhạy cảm.
3. Tạo failure **provider unavailable, expired credential hoặc account locked** và yêu cầu lỗi được biểu diễn rõ, không silent fallback.
4. Viết test tập trung vào **credential không được log và quyết định auth phải nhất quán**.
5. Thử bỏ abstraction; nếu code trực tiếp vẫn rõ và change axis chưa tồn tại, ghi lại lý do chưa áp dụng Factory.

## Câu hỏi review

- Trong miền authentication, phần nào là policy/domain rule và phần nào chỉ là wiring?
- Factory bảo vệ thay đổi **thêm product mới qua creator/factory boundary** bằng cơ chế nào?
- Failure **provider unavailable, expired credential hoặc account locked** được phát hiện, dịch và quan sát ở boundary nào?
- Abstraction có làm invariant **credential không được log và quyết định auth phải nhất quán** dễ test hơn không?
- Complexity mới xuất hiện ở đâu: số class, ordering, lifecycle hay debugging?

## Kết quả mong đợi

Một lời giải đạt yêu cầu phải chứng minh flow authentication vẫn giữ invariant khi thay implementation factory, có assertion cho failure đã nêu và giải thích được trade-off của boundary mới. Không chấm điểm dựa trên số lượng interface hoặc class.
