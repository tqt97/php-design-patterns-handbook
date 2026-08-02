# Speaker Notes — Production Readiness

## Chuẩn bị riêng cho lesson

- Chuẩn bị baseline có lỗi: Provider migration rollout 100% gây duplicate charge và không rollback được.
- Mở diagram và file demo tại điểm quyết định, không mở sẵn solution.
- Chuẩn bị artifact mẫu: go/no-go packet, rollout timeline và rollback evidence.

## Câu hỏi dẫn dắt

- SLO nào bảo vệ customer outcome?
- State nào khó rollback?
- Ai có quyền dừng release?

## Nhịp live coding

Thay vì đọc lại checklist của bài tập, giảng viên yêu cầu học viên mở [exercise.md](exercise.md), chọn một bước rủi ro nhất của **Speaker Notes — Production Readiness**, giải thích giả định đang bảo vệ, test sẽ viết và dấu hiệu production cho biết bước đó thất bại. Sau phần trình bày, đối chiếu với rubric của bài tập.

## Lỗi học viên thường gặp

- Checklist tick-box không evidence.
- Rollback chưa từng chạy.
- Alert không có runbook.

## Failure injection và debrief

Kích hoạt tình huống **Provider migration rollout 100% gây duplicate charge và không rollback được**. Yêu cầu học viên chỉ ra state đã thay đổi, side effect có thể lặp, owner xử lý và test cần thêm. Kết thúc bằng việc cập nhật go/no-go packet, rollout timeline và rollback evidence cùng một revisit trigger.

## Full flow 90 phút

| Thời lượng | Hoạt động | Evidence |
|---:|---|---|
| 0–10 | Nêu tình huống **provider migration** và yêu cầu học viên xác định invariant/failure | Problem statement |
| 10–25 | Vẽ baseline, dependency và điểm thay đổi | Current-state diagram |
| 25–45 | Live coding nhỏ theo chủ đề **Production Readiness** | Commit + test đỏ/xanh |
| 45–60 | Tiêm một failure thực tế, quan sát state và side effect | Failure timeline |
| 60–75 | Nhóm học viên tạo **SLO, rollout, rollback và go/no-go** và phản biện trade-off | Review packet |
| 75–85 | So sánh với baseline đơn giản hơn, quyết định giữ/xóa abstraction | ADR mini |
| 85–90 | Exit ticket: một invariant, một metric, một revisit trigger | Exit note |

### Câu hỏi debrief riêng

- Customer outcome nào quyết định go/no-go?
- Rollback đã được rehearsal với state thật chưa?
- Canary metric nào phân biệt provider lỗi và code lỗi?
- Ai có quyền dừng rollout và runbook ở đâu?
