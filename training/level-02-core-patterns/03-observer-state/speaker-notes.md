# Speaker Notes — Observer State

## Chuẩn bị riêng cho lesson

- Chuẩn bị baseline có lỗi: Email listener lỗi làm rollback OrderPaid hoặc duplicate event gửi hai lần.
- Mở diagram và file demo tại điểm quyết định, không mở sẵn solution.
- Chuẩn bị artifact mẫu: state diagram, event catalog và delivery policy.

## Câu hỏi dẫn dắt

- Reaction nào thuộc invariant?
- Event là fact hay command trá hình?
- Illegal transition trả lỗi nào?

## Nhịp live coding

Thay vì đọc lại checklist của bài tập, giảng viên yêu cầu học viên mở [exercise.md](exercise.md), chọn một bước rủi ro nhất của **Speaker Notes — Observer State**, giải thích giả định đang bảo vệ, test sẽ viết và dấu hiệu production cho biết bước đó thất bại. Sau phần trình bày, đối chiếu với rubric của bài tập.

## Lỗi học viên thường gặp

- Listener sửa aggregate khác trong cùng transaction.
- State chỉ là enum nhưng rule vẫn ở controller.
- Subscriber không có dedupe key.

## Failure injection và debrief

Kích hoạt tình huống **Email listener lỗi làm rollback OrderPaid hoặc duplicate event gửi hai lần**. Yêu cầu học viên chỉ ra state đã thay đổi, side effect có thể lặp, owner xử lý và test cần thêm. Kết thúc bằng việc cập nhật state diagram, event catalog và delivery policy cùng một revisit trigger.

## Full flow 90 phút

| Thời lượng | Hoạt động | Evidence |
|---:|---|---|
| 0–10 | Nêu tình huống **order lifecycle + notifications** và yêu cầu học viên xác định invariant/failure | Problem statement |
| 10–25 | Vẽ baseline, dependency và điểm thay đổi | Current-state diagram |
| 25–45 | Live coding nhỏ theo chủ đề **Observer & State** | Commit + test đỏ/xanh |
| 45–60 | Tiêm một failure thực tế, quan sát state và side effect | Failure timeline |
| 60–75 | Nhóm học viên tạo **state transition và after-commit event** và phản biện trade-off | Review packet |
| 75–85 | So sánh với baseline đơn giản hơn, quyết định giữ/xóa abstraction | ADR mini |
| 85–90 | Exit ticket: một invariant, một metric, một revisit trigger | Exit note |

### Câu hỏi debrief riêng

- Transition nào bất hợp lệ và ai sở hữu guard?
- Event là fact hay command trá hình?
- Subscriber duplicate sẽ làm side effect nào lặp?
- After-commit failure được replay ở đâu?
