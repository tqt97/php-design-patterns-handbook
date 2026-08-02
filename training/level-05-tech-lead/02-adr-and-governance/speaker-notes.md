# Speaker Notes — Adr And Governance

## Chuẩn bị riêng cho lesson

- Chuẩn bị baseline có lỗi: ADR chỉ ghi “dùng best practice” và guardrail chặn use case hợp lệ.
- Mở diagram và file demo tại điểm quyết định, không mở sẵn solution.
- Chuẩn bị artifact mẫu: ADR hoàn chỉnh, fitness rule và exception record.

## Câu hỏi dẫn dắt

- Quyết định này khó đảo ở điểm nào?
- Evidence nào sẽ làm ADR hết đúng?
- Exception được phê duyệt và hết hạn ra sao?

## Nhịp live coding

Thay vì đọc lại checklist của bài tập, giảng viên yêu cầu học viên mở [exercise.md](exercise.md), chọn một bước rủi ro nhất của **Speaker Notes — Adr And Governance**, giải thích giả định đang bảo vệ, test sẽ viết và dấu hiệu production cho biết bước đó thất bại. Sau phần trình bày, đối chiếu với rubric của bài tập.

## Lỗi học viên thường gặp

- ADR là biên bản họp.
- Không cập nhật status superseded.
- Rule CI nhiều false positive.

## Failure injection và debrief

Kích hoạt tình huống **ADR chỉ ghi “dùng best practice” và guardrail chặn use case hợp lệ**. Yêu cầu học viên chỉ ra state đã thay đổi, side effect có thể lặp, owner xử lý và test cần thêm. Kết thúc bằng việc cập nhật ADR hoàn chỉnh, fitness rule và exception record cùng một revisit trigger.

## Full flow 90 phút

| Thời lượng | Hoạt động | Evidence |
|---:|---|---|
| 0–10 | Nêu tình huống **team chọn CQRS theo trend** và yêu cầu học viên xác định invariant/failure | Problem statement |
| 10–25 | Vẽ baseline, dependency và điểm thay đổi | Current-state diagram |
| 25–45 | Live coding nhỏ theo chủ đề **ADR & Governance** | Commit + test đỏ/xanh |
| 45–60 | Tiêm một failure thực tế, quan sát state và side effect | Failure timeline |
| 60–75 | Nhóm học viên tạo **decision drivers, exception và revisit** và phản biện trade-off | Review packet |
| 75–85 | So sánh với baseline đơn giản hơn, quyết định giữ/xóa abstraction | ADR mini |
| 85–90 | Exit ticket: một invariant, một metric, một revisit trigger | Exit note |

### Câu hỏi debrief riêng

- Decision driver nào có thể thay đổi sớm nhất?
- Exception process ngăn governance thành bureaucracy thế nào?
- Fitness function nào kiểm tra rule tự động?
- Ngày/revisit trigger nào buộc ADR được đọc lại?
