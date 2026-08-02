# Speaker Notes — Strategy Factory

## Chuẩn bị riêng cho lesson

- Chuẩn bị baseline có lỗi: Chọn sai shipping policy theo tenant làm báo giá không nhất quán.
- Mở diagram và file demo tại điểm quyết định, không mở sẵn solution.
- Chuẩn bị artifact mẫu: policy contract, selection table và contract tests.

## Câu hỏi dẫn dắt

- Factory chỉ tạo object hay đang chứa business rule?
- Policy nào có thể thay runtime?
- Nếu chỉ hai case ổn định, pattern có đáng không?

## Nhịp live coding

Thay vì đọc lại checklist của bài tập, giảng viên yêu cầu học viên mở [exercise.md](exercise.md), chọn một bước rủi ro nhất của **Speaker Notes — Strategy Factory**, giải thích giả định đang bảo vệ, test sẽ viết và dấu hiệu production cho biết bước đó thất bại. Sau phần trình bày, đối chiếu với rubric của bài tập.

## Lỗi học viên thường gặp

- Strategy trả đơn vị tiền khác nhau.
- Factory phụ thuộc request toàn cục.
- Caller biết concrete strategy.

## Failure injection và debrief

Kích hoạt tình huống **Chọn sai shipping policy theo tenant làm báo giá không nhất quán**. Yêu cầu học viên chỉ ra state đã thay đổi, side effect có thể lặp, owner xử lý và test cần thêm. Kết thúc bằng việc cập nhật policy contract, selection table và contract tests cùng một revisit trigger.

## Full flow 90 phút

| Thời lượng | Hoạt động | Evidence |
|---:|---|---|
| 0–10 | Nêu tình huống **shipping policy selection** và yêu cầu học viên xác định invariant/failure | Problem statement |
| 10–25 | Vẽ baseline, dependency và điểm thay đổi | Current-state diagram |
| 25–45 | Live coding nhỏ theo chủ đề **Strategy & Factory** | Commit + test đỏ/xanh |
| 45–60 | Tiêm một failure thực tế, quan sát state và side effect | Failure timeline |
| 60–75 | Nhóm học viên tạo **policy contract và composition root** và phản biện trade-off | Review packet |
| 75–85 | So sánh với baseline đơn giản hơn, quyết định giữ/xóa abstraction | ADR mini |
| 85–90 | Exit ticket: một invariant, một metric, một revisit trigger | Exit note |

### Câu hỏi debrief riêng

- Policy nào là trục thay đổi, factory nào chỉ làm wiring?
- Contract test nào mọi strategy phải vượt qua?
- Selector sai tenant sẽ tạo customer impact gì?
- Khi chỉ có hai case ổn định, match có đơn giản hơn không?
