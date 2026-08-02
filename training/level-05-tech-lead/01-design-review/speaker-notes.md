# Speaker Notes — Design Review

## Chuẩn bị riêng cho lesson

- Chuẩn bị baseline có lỗi: Review biến thành tranh luận style, bỏ sót invariant và rollback.
- Mở diagram và file demo tại điểm quyết định, không mở sẵn solution.
- Chuẩn bị artifact mẫu: review packet, risk register và decision log.

## Câu hỏi dẫn dắt

- Assumption nào chưa kiểm chứng?
- Failure nào có blast radius lớn nhất?
- Có cách rollback mà không data loss?

## Nhịp live coding

Thay vì đọc lại checklist của bài tập, giảng viên yêu cầu học viên mở [exercise.md](exercise.md), chọn một bước rủi ro nhất của **Speaker Notes — Design Review**, giải thích giả định đang bảo vệ, test sẽ viết và dấu hiệu production cho biết bước đó thất bại. Sau phần trình bày, đối chiếu với rubric của bài tập.

## Lỗi học viên thường gặp

- Review sơ đồ mà không trace scenario.
- Không có option do-nothing.
- Action item không owner.

## Failure injection và debrief

Kích hoạt tình huống **Review biến thành tranh luận style, bỏ sót invariant và rollback**. Yêu cầu học viên chỉ ra state đã thay đổi, side effect có thể lặp, owner xử lý và test cần thêm. Kết thúc bằng việc cập nhật review packet, risk register và decision log cùng một revisit trigger.

## Full flow 90 phút

| Thời lượng | Hoạt động | Evidence |
|---:|---|---|
| 0–10 | Nêu tình huống **PR thêm 12 interface** và yêu cầu học viên xác định invariant/failure | Problem statement |
| 10–25 | Vẽ baseline, dependency và điểm thay đổi | Current-state diagram |
| 25–45 | Live coding nhỏ theo chủ đề **Design Review** | Commit + test đỏ/xanh |
| 45–60 | Tiêm một failure thực tế, quan sát state và side effect | Failure timeline |
| 60–75 | Nhóm học viên tạo **forces, evidence và reversibility** và phản biện trade-off | Review packet |
| 75–85 | So sánh với baseline đơn giản hơn, quyết định giữ/xóa abstraction | ADR mini |
| 85–90 | Exit ticket: một invariant, một metric, một revisit trigger | Exit note |

### Câu hỏi debrief riêng

- Abstraction nào có evidence từ hai use case thật?
- Failure/recovery có được review ngang với happy path?
- Decision có reversible không và chi phí rollback?
- Reviewer cần dashboard hoặc incident evidence nào?
