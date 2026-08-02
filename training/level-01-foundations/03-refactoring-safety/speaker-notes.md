# Speaker Notes — Refactoring Safety

## Chuẩn bị riêng cho lesson

- Chuẩn bị baseline có lỗi: Legacy pricing có nhánh ngầm và side effect khiến refactor đổi behavior ngoài ý muốn.
- Mở diagram và file demo tại điểm quyết định, không mở sẵn solution.
- Chuẩn bị artifact mẫu: behavior inventory, seam diagram và mismatch report.

## Câu hỏi dẫn dắt

- Behavior nào là bug nhưng chưa được phép sửa trong refactor?
- Seam nhỏ nhất nằm ở đâu?
- Rollback trong một deploy được thực hiện thế nào?

## Nhịp live coding

Thay vì đọc lại checklist của bài tập, giảng viên yêu cầu học viên mở [exercise.md](exercise.md), chọn một bước rủi ro nhất của **Speaker Notes — Refactoring Safety**, giải thích giả định đang bảo vệ, test sẽ viết và dấu hiệu production cho biết bước đó thất bại. Sau phần trình bày, đối chiếu với rubric của bài tập.

## Lỗi học viên thường gặp

- Refactor và redesign cùng commit.
- Golden master quá lớn không giải thích failure.
- Xóa path cũ trước khi có telemetry.

## Failure injection và debrief

Kích hoạt tình huống **Legacy pricing có nhánh ngầm và side effect khiến refactor đổi behavior ngoài ý muốn**. Yêu cầu học viên chỉ ra state đã thay đổi, side effect có thể lặp, owner xử lý và test cần thêm. Kết thúc bằng việc cập nhật behavior inventory, seam diagram và mismatch report cùng một revisit trigger.

## Full flow 90 phút

| Thời lượng | Hoạt động | Evidence |
|---:|---|---|
| 0–10 | Nêu tình huống **legacy branch không có test** và yêu cầu học viên xác định invariant/failure | Problem statement |
| 10–25 | Vẽ baseline, dependency và điểm thay đổi | Current-state diagram |
| 25–45 | Live coding nhỏ theo chủ đề **Safe refactoring** | Commit + test đỏ/xanh |
| 45–60 | Tiêm một failure thực tế, quan sát state và side effect | Failure timeline |
| 60–75 | Nhóm học viên tạo **characterization test và small commits** và phản biện trade-off | Review packet |
| 75–85 | So sánh với baseline đơn giản hơn, quyết định giữ/xóa abstraction | ADR mini |
| 85–90 | Exit ticket: một invariant, một metric, một revisit trigger | Exit note |

### Câu hỏi debrief riêng

- Characterization test nào khóa behavior quan trọng nhất?
- Seam nào cho phép thay từng phần mà không big-bang?
- Commit nào có thể rollback độc lập?
- Dấu hiệu nào cho phép xóa code path cũ?
