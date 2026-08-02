# Playground 107: Repository — Support

## Mục tiêu học tập

Chạy một ví dụ nhỏ về **phân loại ticket và định tuyến đội xử lý** và quan sát cách **Repository** giúp che persistence cho aggregate. Playground này là drill 10–15 phút; flagship playground phù hợp hơn cho luồng nhiều bước.

Sau bài này, người học phải giải thích được **domain thao tác collection thay vì persistence API** trong bối cảnh support, chỉ ra dependency đã bị đảo hoặc che giấu, và nêu trường hợp giải pháp trực tiếp vẫn tốt hơn.

## Bối cảnh và invariant

- **Nghiệp vụ:** ticket category, SLA, escalation và assignment.
- **Invariant:** ticket không được mất owner, priority hoặc audit trail.
- **Change axis:** thay storage hoặc query implementation.
- **Failure bắt buộc quan sát:** rule không match, escalation loop hoặc duplicate action; ở mức pattern cần chú ý thêm repository thành wrapper CRUD, tenant filter bị bỏ sót.

```mermaid
flowchart LR
    A[SupportTicket] --> S[Application service]
    S --> R[Repository contract]
    R --> M[In-memory adapter]
    R --> D[(Database adapter)]
    M --> E[Ticket decision]
    D --> E
    D -. conflict .-> F[Lost escalation]
```

## Cách chạy

```bash
php playground/107-repository-support/index.php
```

Trước khi chạy, dự đoán output và ghi lại concrete detail mà client đang biết. Sau khi chạy, đối chiếu xem **che persistence và diễn đạt query theo domain** đã làm client biết ít đi điều gì, đồng thời kiểm tra invariant của support vẫn được giữ.

## Thử nghiệm có hướng dẫn

1. Chạy baseline và lưu output/error hiện tại.
2. Thử ticket vượt sla và kiểm tra escalation chỉ chạy một lần.
3. Tạo failure **rule không match, escalation loop hoặc duplicate action** và yêu cầu lỗi được biểu diễn rõ, không silent fallback.
4. Viết test tập trung vào **ticket không được mất owner, priority hoặc audit trail**.
5. Thử bỏ abstraction; nếu code trực tiếp vẫn rõ và change axis chưa tồn tại, ghi lại lý do chưa áp dụng Repository.

## Câu hỏi review

- Trong miền support, phần nào là policy/domain rule và phần nào chỉ là wiring?
- Repository bảo vệ thay đổi **thay storage hoặc query implementation** bằng cơ chế nào?
- Failure **rule không match, escalation loop hoặc duplicate action** được phát hiện, dịch và quan sát ở boundary nào?
- Abstraction có làm invariant **ticket không được mất owner, priority hoặc audit trail** dễ test hơn không?
- Complexity mới xuất hiện ở đâu: số class, ordering, lifecycle hay debugging?

## Kết quả mong đợi

Một lời giải đạt yêu cầu phải chứng minh flow support vẫn giữ invariant khi thay implementation repository, có assertion cho failure đã nêu và giải thích được trade-off của boundary mới. Không chấm điểm dựa trên số lượng interface hoặc class.
