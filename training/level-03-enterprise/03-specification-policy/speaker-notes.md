# Speaker Notes — Specification Policy

## Chuẩn bị riêng cho lesson

- Chuẩn bị baseline có lỗi: Promotion áp dụng sai vì rule compose không giải thích lý do từ chối.
- Mở diagram và file demo tại điểm quyết định, không mở sẵn solution.
- Chuẩn bị artifact mẫu: rule tree, reason-code table và generated cases.

## Câu hỏi dẫn dắt

- Rule nào là predicate, rule nào chọn policy?
- Specification có phụ thuộc ORM không?
- Explainability được lưu ở đâu?

## Nhịp live coding

Thay vì đọc lại checklist của bài tập, giảng viên yêu cầu học viên mở [exercise.md](exercise.md), chọn một bước rủi ro nhất của **Speaker Notes — Specification Policy**, giải thích giả định đang bảo vệ, test sẽ viết và dấu hiệu production cho biết bước đó thất bại. Sau phần trình bày, đối chiếu với rubric của bài tập.

## Lỗi học viên thường gặp

- Specification chỉ là wrapper closure vô nghĩa.
- Mix query optimization với domain rule.
- Không test timezone/boundary.

## Failure injection và debrief

Kích hoạt tình huống **Promotion áp dụng sai vì rule compose không giải thích lý do từ chối**. Yêu cầu học viên chỉ ra state đã thay đổi, side effect có thể lặp, owner xử lý và test cần thêm. Kết thúc bằng việc cập nhật rule tree, reason-code table và generated cases cùng một revisit trigger.

## Full flow 90 phút

| Thời lượng | Hoạt động | Evidence |
|---:|---|---|
| 0–10 | Nêu tình huống **discount eligibility** và yêu cầu học viên xác định invariant/failure | Problem statement |
| 10–25 | Vẽ baseline, dependency và điểm thay đổi | Current-state diagram |
| 25–45 | Live coding nhỏ theo chủ đề **Specification & Policy** | Commit + test đỏ/xanh |
| 45–60 | Tiêm một failure thực tế, quan sát state và side effect | Failure timeline |
| 60–75 | Nhóm học viên tạo **reason codes và rule composition** và phản biện trade-off | Review packet |
| 75–85 | So sánh với baseline đơn giản hơn, quyết định giữ/xóa abstraction | ADR mini |
| 85–90 | Exit ticket: một invariant, một metric, một revisit trigger | Exit note |

### Câu hỏi debrief riêng

- Rule nào là eligibility, rule nào là calculation?
- Reason code có đủ cho support và analytics không?
- Composition AND/OR có short-circuit hay gom lý do?
- Policy version được lưu với quyết định ở đâu?
