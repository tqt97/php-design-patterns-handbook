# Speaker Notes — Adapter Decorator

## Chuẩn bị riêng cho lesson

- Chuẩn bị baseline có lỗi: Provider timeout sau khi đã nhận request và wrapper retry gửi trùng notification.
- Mở diagram và file demo tại điểm quyết định, không mở sẵn solution.
- Chuẩn bị artifact mẫu: wrapper order diagram và provider contract fixtures.

## Câu hỏi dẫn dắt

- Failure nào adapter phải translate?
- Decorator order thay đổi side effect ra sao?
- Retry có an toàn khi provider không hỗ trợ idempotency?

## Nhịp live coding

Thay vì đọc lại checklist của bài tập, giảng viên yêu cầu học viên mở [exercise.md](exercise.md), chọn một bước rủi ro nhất của **Speaker Notes — Adapter Decorator**, giải thích giả định đang bảo vệ, test sẽ viết và dấu hiệu production cho biết bước đó thất bại. Sau phần trình bày, đối chiếu với rubric của bài tập.

## Lỗi học viên thường gặp

- Adapter leak SDK DTO.
- Retry decorator bọc ngoài idempotency sai thứ tự.
- Logging làm lộ secret.

## Failure injection và debrief

Kích hoạt tình huống **Provider timeout sau khi đã nhận request và wrapper retry gửi trùng notification**. Yêu cầu học viên chỉ ra state đã thay đổi, side effect có thể lặp, owner xử lý và test cần thêm. Kết thúc bằng việc cập nhật wrapper order diagram và provider contract fixtures cùng một revisit trigger.

## Full flow 90 phút

| Thời lượng | Hoạt động | Evidence |
|---:|---|---|
| 0–10 | Nêu tình huống **legacy SDK + retry/logging** và yêu cầu học viên xác định invariant/failure | Problem statement |
| 10–25 | Vẽ baseline, dependency và điểm thay đổi | Current-state diagram |
| 25–45 | Live coding nhỏ theo chủ đề **Adapter & Decorator** | Commit + test đỏ/xanh |
| 45–60 | Tiêm một failure thực tế, quan sát state và side effect | Failure timeline |
| 60–75 | Nhóm học viên tạo **translation boundary và wrapper order** và phản biện trade-off | Review packet |
| 75–85 | So sánh với baseline đơn giản hơn, quyết định giữ/xóa abstraction | ADR mini |
| 85–90 | Exit ticket: một invariant, một metric, một revisit trigger | Exit note |

### Câu hỏi debrief riêng

- Adapter đang dịch contract hay che business rule?
- Wrapper order thay đổi retry/logging/validation ra sao?
- Timeout sau success được map thành trạng thái nào?
- Contract test nào bảo vệ khi nâng SDK?
