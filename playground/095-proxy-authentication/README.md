# Playground 95: Proxy — Authentication

## Mục tiêu học tập

Quan sát Proxy trong miền authentication.

Sau bài này, người học phải giải thích được **kiểm soát truy cập vào object thật** trong bối cảnh authentication, chỉ ra dependency đã bị đảo hoặc che giấu, và nêu trường hợp giải pháp trực tiếp vẫn tốt hơn.

## Bối cảnh và invariant

- **Nghiệp vụ:** credential verifier, identity và auth result.
- **Invariant:** credential không được log và quyết định auth phải nhất quán.
- **Change axis:** thêm access policy mà giữ interface.
- **Failure bắt buộc quan sát:** provider unavailable, expired credential hoặc account locked; ở mức pattern cần chú ý thêm proxy thay đổi semantics, cache stale hoặc bypass authorization.

```mermaid
sequenceDiagram
    participant U as Client
    participant P as Proxy
    participant S as IdentityAdapter
    U->>P: LoginRequest
    P->>P: authorize/cache/rate-limit
    alt allowed
      P->>S: delegate
      S-->>P: Auth result
      P-->>U: result
    else blocked
      P-->>U: explicit Token replay
    end
```

## Cách chạy

```bash
php playground/095-proxy-authentication/index.php
```

Trước khi chạy, dự đoán output và ghi lại concrete detail mà client đang biết. Sau khi chạy, đối chiếu xem **intercept authorization, cache hoặc lazy loading** đã làm client biết ít đi điều gì, đồng thời kiểm tra invariant của authentication vẫn được giữ.

## Thử nghiệm có hướng dẫn

1. Chạy baseline và lưu output/error hiện tại.
2. Thử account locked và bảo đảm lỗi không rò thông tin nhạy cảm.
3. Tạo failure **provider unavailable, expired credential hoặc account locked** và yêu cầu lỗi được biểu diễn rõ, không silent fallback.
4. Viết test tập trung vào **credential không được log và quyết định auth phải nhất quán**.
5. Thử bỏ abstraction; nếu code trực tiếp vẫn rõ và change axis chưa tồn tại, ghi lại lý do chưa áp dụng Proxy.

## Câu hỏi review

- Trong miền authentication, phần nào là policy/domain rule và phần nào chỉ là wiring?
- Proxy bảo vệ thay đổi **thêm access policy mà giữ interface** bằng cơ chế nào?
- Failure **provider unavailable, expired credential hoặc account locked** được phát hiện, dịch và quan sát ở boundary nào?
- Abstraction có làm invariant **credential không được log và quyết định auth phải nhất quán** dễ test hơn không?
- Complexity mới xuất hiện ở đâu: số class, ordering, lifecycle hay debugging?

## Kết quả mong đợi

Một lời giải đạt yêu cầu phải chứng minh flow authentication vẫn giữ invariant khi thay implementation proxy, có assertion cho failure đã nêu và giải thích được trade-off của boundary mới. Không chấm điểm dựa trên số lượng interface hoặc class.
