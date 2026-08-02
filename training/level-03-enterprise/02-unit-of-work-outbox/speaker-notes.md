# Speaker Notes — Unit Of Work Outbox

## Chuẩn bị riêng cho lesson

- Chuẩn bị baseline có lỗi: Order commit thành công nhưng event không publish, hoặc publish lặp sau crash.
- Mở diagram và file demo tại điểm quyết định, không mở sẵn solution.
- Chuẩn bị artifact mẫu: crash matrix, outbox sequence và reconciliation query.

## Câu hỏi dẫn dắt

- Điểm crash nào tạo duplicate?
- Nested transaction semantics là gì?
- Reconciliation tìm missing event thế nào?

## Nhịp live coding

Thay vì đọc lại checklist của bài tập, giảng viên yêu cầu học viên mở [exercise.md](exercise.md), chọn một bước rủi ro nhất của **Speaker Notes — Unit Of Work Outbox**, giải thích giả định đang bảo vệ, test sẽ viết và dấu hiệu production cho biết bước đó thất bại. Sau phần trình bày, đối chiếu với rubric của bài tập.

## Lỗi học viên thường gặp

- Publish trước commit.
- Cho rằng broker exactly-once giải hết.
- Outbox không có retention/index.

## Failure injection và debrief

Kích hoạt tình huống **Order commit thành công nhưng event không publish, hoặc publish lặp sau crash**. Yêu cầu học viên chỉ ra state đã thay đổi, side effect có thể lặp, owner xử lý và test cần thêm. Kết thúc bằng việc cập nhật crash matrix, outbox sequence và reconciliation query cùng một revisit trigger.

## Full flow 90 phút

| Thời lượng | Hoạt động | Evidence |
|---:|---|---|
| 0–10 | Nêu tình huống **DB commit + event publish** và yêu cầu học viên xác định invariant/failure | Problem statement |
| 10–25 | Vẽ baseline, dependency và điểm thay đổi | Current-state diagram |
| 25–45 | Live coding nhỏ theo chủ đề **Unit of Work & Outbox** | Commit + test đỏ/xanh |
| 45–60 | Tiêm một failure thực tế, quan sát state và side effect | Failure timeline |
| 60–75 | Nhóm học viên tạo **crash matrix và duplicate consumer** và phản biện trade-off | Review packet |
| 75–85 | So sánh với baseline đơn giản hơn, quyết định giữ/xóa abstraction | ADR mini |
| 85–90 | Exit ticket: một invariant, một metric, một revisit trigger | Exit note |

### Câu hỏi debrief riêng

- Transaction nào phải atomic?
- Crash sau publish trước mark tạo duplicate gì?
- Consumer dedup dựa vào key nào?
- Outbox backlog bao lâu thì vi phạm SLO?
