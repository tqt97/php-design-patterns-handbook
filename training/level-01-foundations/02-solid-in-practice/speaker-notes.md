# Speaker Notes — Solid In Practice

## Chuẩn bị riêng cho lesson

- Chuẩn bị baseline có lỗi: Một NotificationService đổi vì template, provider, retry và audit.
- Mở diagram và file demo tại điểm quyết định, không mở sẵn solution.
- Chuẩn bị artifact mẫu: SOLID violation map và contract test matrix.

## Câu hỏi dẫn dắt

- Principle nào đang bị vi phạm và bằng chứng là gì?
- Nếu provider trả accepted thay vì delivered, contract có còn đúng?
- Interface nào chỉ phục vụ một client?

## Nhịp live coding

Thay vì đọc lại checklist của bài tập, giảng viên yêu cầu học viên mở [exercise.md](exercise.md), chọn một bước rủi ro nhất của **Speaker Notes — Solid In Practice**, giải thích giả định đang bảo vệ, test sẽ viết và dấu hiệu production cho biết bước đó thất bại. Sau phần trình bày, đối chiếu với rubric của bài tập.

## Lỗi học viên thường gặp

- Mỗi method thành một interface.
- OCP bằng switch trong factory khổng lồ.
- DIP nhưng domain vẫn biết SDK exception.

## Failure injection và debrief

Kích hoạt tình huống **Một NotificationService đổi vì template, provider, retry và audit**. Yêu cầu học viên chỉ ra state đã thay đổi, side effect có thể lặp, owner xử lý và test cần thêm. Kết thúc bằng việc cập nhật SOLID violation map và contract test matrix cùng một revisit trigger.

## Full flow 90 phút

| Thời lượng | Hoạt động | Evidence |
|---:|---|---|
| 0–10 | Nêu tình huống **thêm provider làm sửa nhiều class** và yêu cầu học viên xác định invariant/failure | Problem statement |
| 10–25 | Vẽ baseline, dependency và điểm thay đổi | Current-state diagram |
| 25–45 | Live coding nhỏ theo chủ đề **SOLID** | Commit + test đỏ/xanh |
| 45–60 | Tiêm một failure thực tế, quan sát state và side effect | Failure timeline |
| 60–75 | Nhóm học viên tạo **dependency graph và change impact** và phản biện trade-off | Review packet |
| 75–85 | So sánh với baseline đơn giản hơn, quyết định giữ/xóa abstraction | ADR mini |
| 85–90 | Exit ticket: một invariant, một metric, một revisit trigger | Exit note |

### Câu hỏi debrief riêng

- Nguyên tắc nào đang bị vi phạm vì change reason nào?
- Interface mới có consumer thật hay chỉ dự đoán tương lai?
- Dependency direction thay đổi blast radius ra sao?
- Metric nào cho thấy refactor giúp delivery nhanh hơn?
