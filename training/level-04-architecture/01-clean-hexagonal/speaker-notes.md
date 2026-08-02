# Speaker Notes — Clean Hexagonal

## Chuẩn bị riêng cho lesson

- Chuẩn bị baseline có lỗi: Use case phụ thuộc Laravel Request, Eloquent và Facade nên không test độc lập.
- Mở diagram và file demo tại điểm quyết định, không mở sẵn solution.
- Chuẩn bị artifact mẫu: dependency diagram và boundary test portfolio.

## Câu hỏi dẫn dắt

- Dependency nào đang hướng sai?
- Adapter chịu mapping/error nào?
- Framework upgrade tác động core ra sao?

## Nhịp live coding

Thay vì đọc lại checklist của bài tập, giảng viên yêu cầu học viên mở [exercise.md](exercise.md), chọn một bước rủi ro nhất của **Speaker Notes — Clean Hexagonal**, giải thích giả định đang bảo vệ, test sẽ viết và dấu hiệu production cho biết bước đó thất bại. Sau phần trình bày, đối chiếu với rubric của bài tập.

## Lỗi học viên thường gặp

- Ports cho mọi class nội bộ.
- Domain gọi container.
- DTO framework đi xuyên boundary.

## Failure injection và debrief

Kích hoạt tình huống **Use case phụ thuộc Laravel Request, Eloquent và Facade nên không test độc lập**. Yêu cầu học viên chỉ ra state đã thay đổi, side effect có thể lặp, owner xử lý và test cần thêm. Kết thúc bằng việc cập nhật dependency diagram và boundary test portfolio cùng một revisit trigger.

## Full flow 90 phút

| Thời lượng | Hoạt động | Evidence |
|---:|---|---|
| 0–10 | Nêu tình huống **framework-bound use case** và yêu cầu học viên xác định invariant/failure | Problem statement |
| 10–25 | Vẽ baseline, dependency và điểm thay đổi | Current-state diagram |
| 25–45 | Live coding nhỏ theo chủ đề **Clean/Hexagonal** | Commit + test đỏ/xanh |
| 45–60 | Tiêm một failure thực tế, quan sát state và side effect | Failure timeline |
| 60–75 | Nhóm học viên tạo **ports, adapters và composition root** và phản biện trade-off | Review packet |
| 75–85 | So sánh với baseline đơn giản hơn, quyết định giữ/xóa abstraction | ADR mini |
| 85–90 | Exit ticket: một invariant, một metric, một revisit trigger | Exit note |

### Câu hỏi debrief riêng

- Port có vocabulary use case hay technology?
- Composition root nào tạo graph cho HTTP/CLI/worker?
- Framework type đang rò vào domain ở đâu?
- Adapter contract test bảo vệ migration nào?
