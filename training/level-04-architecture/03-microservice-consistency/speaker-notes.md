# Speaker Notes — Microservice Consistency

## Chuẩn bị riêng cho lesson

- Chuẩn bị baseline có lỗi: Payment confirmed nhưng stock reject; event đến lặp và đảo thứ tự.
- Mở diagram và file demo tại điểm quyết định, không mở sẵn solution.
- Chuẩn bị artifact mẫu: saga state machine, message contract và stuck-process dashboard.

## Câu hỏi dẫn dắt

- Ai sở hữu workflow state?
- Compensation có thực sự đảo ngược được không?
- Metric nào phát hiện saga kẹt?

## Nhịp live coding

Thay vì đọc lại checklist của bài tập, giảng viên yêu cầu học viên mở [exercise.md](exercise.md), chọn một bước rủi ro nhất của **Speaker Notes — Microservice Consistency**, giải thích giả định đang bảo vệ, test sẽ viết và dấu hiệu production cho biết bước đó thất bại. Sau phần trình bày, đối chiếu với rubric của bài tập.

## Lỗi học viên thường gặp

- Distributed transaction giả bằng synchronous chain.
- Consumer không lưu inbox.
- Không có terminal failed state.

## Failure injection và debrief

Kích hoạt tình huống **Payment confirmed nhưng stock reject; event đến lặp và đảo thứ tự**. Yêu cầu học viên chỉ ra state đã thay đổi, side effect có thể lặp, owner xử lý và test cần thêm. Kết thúc bằng việc cập nhật saga state machine, message contract và stuck-process dashboard cùng một revisit trigger.

## Full flow 90 phút

| Thời lượng | Hoạt động | Evidence |
|---:|---|---|
| 0–10 | Nêu tình huống **order/payment/inventory saga** và yêu cầu học viên xác định invariant/failure | Problem statement |
| 10–25 | Vẽ baseline, dependency và điểm thay đổi | Current-state diagram |
| 25–45 | Live coding nhỏ theo chủ đề **Microservice Consistency** | Commit + test đỏ/xanh |
| 45–60 | Tiêm một failure thực tế, quan sát state và side effect | Failure timeline |
| 60–75 | Nhóm học viên tạo **outbox, process manager và compensation** và phản biện trade-off | Review packet |
| 75–85 | So sánh với baseline đơn giản hơn, quyết định giữ/xóa abstraction | ADR mini |
| 85–90 | Exit ticket: một invariant, một metric, một revisit trigger | Exit note |

### Câu hỏi debrief riêng

- Process manager lưu state và deadline ở đâu?
- Compensation có thực sự đảo ngược được side effect không?
- Event out-of-order được phát hiện thế nào?
- Khi nào workflow cần manual intervention?
