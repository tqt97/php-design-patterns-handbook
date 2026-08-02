# Speaker Notes — Repository Query Object

## Chuẩn bị riêng cho lesson

- Chuẩn bị baseline có lỗi: Dùng aggregate repository cho dashboard gây N+1 và load object graph lớn.
- Mở diagram và file demo tại điểm quyết định, không mở sẵn solution.
- Chuẩn bị artifact mẫu: write/read boundary map và query evidence.

## Câu hỏi dẫn dắt

- Method nào thuộc write model, method nào thuộc reporting?
- Repository có che semantics hay chỉ bọc ORM?
- Projection có freshness requirement gì?

## Nhịp live coding

Thay vì đọc lại checklist của bài tập, giảng viên yêu cầu học viên mở [exercise.md](exercise.md), chọn một bước rủi ro nhất của **Speaker Notes — Repository Query Object**, giải thích giả định đang bảo vệ, test sẽ viết và dấu hiệu production cho biết bước đó thất bại. Sau phần trình bày, đối chiếu với rubric của bài tập.

## Lỗi học viên thường gặp

- Generic CRUD repository.
- Trả ORM builder qua interface.
- Query Object chứa business mutation.

## Failure injection và debrief

Kích hoạt tình huống **Dùng aggregate repository cho dashboard gây N+1 và load object graph lớn**. Yêu cầu học viên chỉ ra state đã thay đổi, side effect có thể lặp, owner xử lý và test cần thêm. Kết thúc bằng việc cập nhật write/read boundary map và query evidence cùng một revisit trigger.

## Full flow 90 phút

| Thời lượng | Hoạt động | Evidence |
|---:|---|---|
| 0–10 | Nêu tình huống **aggregate write + report read** và yêu cầu học viên xác định invariant/failure | Problem statement |
| 10–25 | Vẽ baseline, dependency và điểm thay đổi | Current-state diagram |
| 25–45 | Live coding nhỏ theo chủ đề **Repository & Query Object** | Commit + test đỏ/xanh |
| 45–60 | Tiêm một failure thực tế, quan sát state và side effect | Failure timeline |
| 60–75 | Nhóm học viên tạo **write/read boundary và cursor** và phản biện trade-off | Review packet |
| 75–85 | So sánh với baseline đơn giản hơn, quyết định giữ/xóa abstraction | ADR mini |
| 85–90 | Exit ticket: một invariant, một metric, một revisit trigger | Exit note |

### Câu hỏi debrief riêng

- Write model invariant nào không được đưa vào query object?
- Repository contract có collection semantics hay chỉ bọc ORM?
- Cursor giữ ổn định khi dữ liệu thay đổi thế nào?
- Replica stale được biểu diễn cho caller ra sao?
