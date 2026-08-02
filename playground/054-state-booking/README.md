# Playground 54: State — Booking

## Mục tiêu học tập

Chạy một ví dụ nhỏ về **hold, confirm, cancel và expire booking** và quan sát cách **State** giúp đưa hành vi/transition vào state object. Playground này là drill 10–15 phút; flagship playground phù hợp hơn cho luồng nhiều bước.

Sau bài này, người học phải giải thích được **hành vi thay đổi theo lifecycle** trong bối cảnh booking, chỉ ra dependency đã bị đảo hoặc che giấu, và nêu trường hợp giải pháp trực tiếp vẫn tốt hơn.

## Bối cảnh và invariant

- **Nghiệp vụ:** availability interval, hold TTL và confirmation.
- **Invariant:** một resource không thể được xác nhận trùng thời gian.
- **Change axis:** thêm state/transition mà không tạo switch lớn.
- **Failure bắt buộc quan sát:** hold hết hạn, overlap hoặc payment/confirmation lệch trạng thái; ở mức pattern cần chú ý thêm illegal transition hoặc side effect chạy sai thời điểm.

```mermaid
stateDiagram-v2
    [*] --> Draft
    Draft --> Pending: validate BookingCommand
    Pending --> Confirmed: reserve/commit
    Pending --> Failed: Overbooking
    Confirmed --> Cancelled: compensating command
    Failed --> Pending: explicit retry
    Cancelled --> [*]
```

## Cách chạy

```bash
php playground/054-state-booking/index.php
```

Trước khi chạy, dự đoán output và ghi lại concrete detail mà client đang biết. Sau khi chạy, đối chiếu xem **mỗi state sở hữu transition hợp lệ** đã làm client biết ít đi điều gì, đồng thời kiểm tra invariant của booking vẫn được giữ.

## Thử nghiệm có hướng dẫn

1. Chạy baseline và lưu output/error hiện tại.
2. Tạo hai request cạnh tranh và kiểm tra chỉ một booking được xác nhận.
3. Tạo failure **hold hết hạn, overlap hoặc payment/confirmation lệch trạng thái** và yêu cầu lỗi được biểu diễn rõ, không silent fallback.
4. Viết test tập trung vào **một resource không thể được xác nhận trùng thời gian**.
5. Thử bỏ abstraction; nếu code trực tiếp vẫn rõ và change axis chưa tồn tại, ghi lại lý do chưa áp dụng State.

## Câu hỏi review

- Trong miền booking, phần nào là policy/domain rule và phần nào chỉ là wiring?
- State bảo vệ thay đổi **thêm state/transition mà không tạo switch lớn** bằng cơ chế nào?
- Failure **hold hết hạn, overlap hoặc payment/confirmation lệch trạng thái** được phát hiện, dịch và quan sát ở boundary nào?
- Abstraction có làm invariant **một resource không thể được xác nhận trùng thời gian** dễ test hơn không?
- Complexity mới xuất hiện ở đâu: số class, ordering, lifecycle hay debugging?

## Kết quả mong đợi

Một lời giải đạt yêu cầu phải chứng minh flow booking vẫn giữ invariant khi thay implementation state, có assertion cho failure đã nêu và giải thích được trade-off của boundary mới. Không chấm điểm dựa trên số lượng interface hoặc class.
