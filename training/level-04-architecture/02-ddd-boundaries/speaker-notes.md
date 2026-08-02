# Speaker Notes — Ddd Boundaries

## Chuẩn bị riêng cho lesson

- Chuẩn bị baseline có lỗi: Sales và Fulfillment dùng chung Order model nhưng hiểu status khác nhau.
- Mở diagram và file demo tại điểm quyết định, không mở sẵn solution.
- Chuẩn bị artifact mẫu: context map, glossary và aggregate cards.

## Câu hỏi dẫn dắt

- Từ nào đang overload nghĩa?
- Invariant nào thực sự cần atomic cross-context?
- Upstream/downstream power ảnh hưởng integration thế nào?

## Nhịp live coding

Thay vì đọc lại checklist của bài tập, giảng viên yêu cầu học viên mở [exercise.md](exercise.md), chọn một bước rủi ro nhất của **Speaker Notes — Ddd Boundaries**, giải thích giả định đang bảo vệ, test sẽ viết và dấu hiệu production cho biết bước đó thất bại. Sau phần trình bày, đối chiếu với rubric của bài tập.

## Lỗi học viên thường gặp

- Bounded context bằng microservice folder.
- Shared database entity giữa contexts.
- Domain event dump aggregate.

## Failure injection và debrief

Kích hoạt tình huống **Sales và Fulfillment dùng chung Order model nhưng hiểu status khác nhau**. Yêu cầu học viên chỉ ra state đã thay đổi, side effect có thể lặp, owner xử lý và test cần thêm. Kết thúc bằng việc cập nhật context map, glossary và aggregate cards cùng một revisit trigger.

## Full flow 90 phút

| Thời lượng | Hoạt động | Evidence |
|---:|---|---|
| 0–10 | Nêu tình huống **CRM identity + consent** và yêu cầu học viên xác định invariant/failure | Problem statement |
| 10–25 | Vẽ baseline, dependency và điểm thay đổi | Current-state diagram |
| 25–45 | Live coding nhỏ theo chủ đề **DDD Boundaries** | Commit + test đỏ/xanh |
| 45–60 | Tiêm một failure thực tế, quan sát state và side effect | Failure timeline |
| 60–75 | Nhóm học viên tạo **aggregate, bounded context và ACL** và phản biện trade-off | Review packet |
| 75–85 | So sánh với baseline đơn giản hơn, quyết định giữ/xóa abstraction | ADR mini |
| 85–90 | Exit ticket: một invariant, một metric, một revisit trigger | Exit note |

### Câu hỏi debrief riêng

- Invariant nào cần consistency trong cùng aggregate?
- Bounded context nào sở hữu thuật ngữ đang tranh chấp?
- ACL dịch model nào và giữ lỗi nào?
- Context map thay đổi khi power relationship đổi ra sao?
