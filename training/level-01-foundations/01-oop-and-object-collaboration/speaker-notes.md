# Speaker Notes — Oop And Object Collaboration

## Chuẩn bị riêng cho lesson

- Chuẩn bị baseline có lỗi: Cart tự tính discount, tax và persistence nên thay đổi một rule làm vỡ nhiều nơi.
- Mở diagram và file demo tại điểm quyết định, không mở sẵn solution.
- Chuẩn bị artifact mẫu: collaboration diagram và object responsibility cards.

## Câu hỏi dẫn dắt

- Object nào đang bị hỏi dữ liệu rồi quyết định ở bên ngoài?
- Invariant nên nằm ở Cart, Order hay PricingPolicy?
- Mock nào là dấu hiệu boundary đặt sai?

## Nhịp live coding

Thay vì đọc lại checklist của bài tập, giảng viên yêu cầu học viên mở [exercise.md](exercise.md), chọn một bước rủi ro nhất của **Speaker Notes — Oop And Object Collaboration**, giải thích giả định đang bảo vệ, test sẽ viết và dấu hiệu production cho biết bước đó thất bại. Sau phần trình bày, đối chiếu với rubric của bài tập.

## Lỗi học viên thường gặp

- Entity chỉ là data bag.
- Một Manager điều phối mọi thứ.
- Public setter phá invariant.

## Failure injection và debrief

Kích hoạt tình huống **Cart tự tính discount, tax và persistence nên thay đổi một rule làm vỡ nhiều nơi**. Yêu cầu học viên chỉ ra state đã thay đổi, side effect có thể lặp, owner xử lý và test cần thêm. Kết thúc bằng việc cập nhật collaboration diagram và object responsibility cards cùng một revisit trigger.

## Full flow 90 phút

| Thời lượng | Hoạt động | Evidence |
|---:|---|---|
| 0–10 | Nêu tình huống **God object xử lý order** và yêu cầu học viên xác định invariant/failure | Problem statement |
| 10–25 | Vẽ baseline, dependency và điểm thay đổi | Current-state diagram |
| 25–45 | Live coding nhỏ theo chủ đề **Object collaboration** | Commit + test đỏ/xanh |
| 45–60 | Tiêm một failure thực tế, quan sát state và side effect | Failure timeline |
| 60–75 | Nhóm học viên tạo **message sequence và responsibility card** và phản biện trade-off | Review packet |
| 75–85 | So sánh với baseline đơn giản hơn, quyết định giữ/xóa abstraction | ADR mini |
| 85–90 | Exit ticket: một invariant, một metric, một revisit trigger | Exit note |

### Câu hỏi debrief riêng

- Object nào đang giữ invariant nhưng bị client sửa trực tiếp?
- Message nào cho thấy responsibility đặt sai class?
- Một test collaboration nào vẫn cho phép đổi implementation?
- Nếu bỏ abstraction, client phải biết thêm chi tiết gì?
