# Repository Query Object

## Bối cảnh thuyết trình

**Customer write model and reporting** là case xuyên suốt. Giảng viên bắt đầu bằng code hoặc flow trực tiếp, cho học viên dự đoán failure rồi mới giới thiệu vocabulary/pattern.

## Kết quả học tập

- Aggregate repository
- Query projection
- N+1
- Pagination
- Contract test
- Giải thích trade-off và chọn baseline đơn giản hơn khi pattern chưa cần thiết.
- Trình bày test, metric hoặc artifact chứng minh thiết kế hoạt động.

## Luồng hệ thống

```mermaid
flowchart LR
    N0[UseCase] --> N1[Repository / QueryObject]
```

## Agenda 90 phút

| Phút | Hoạt động | Evidence tạo ra |
|---:|---|---|
| 0–8 | Phân tích Dùng aggregate repository cho dashboard gây N và chốt invariant | Một câu invariant có thể kiểm thử |
| 8–20 | Vẽ current flow và failure | Diagram có source of truth |
| 20–38 | Giải thích concept trọng tâm | write/read boundary map và query evidence |
| 38–58 | Live coding theo safety net | Commit nhỏ, demo vẫn chạy |
| 58–73 | Failure injection | Log/output chứng minh behavior |
| 73–84 | Pair review và alternative | Trade-off note |
| 84–90 | Trình bày 3 phút | Decision + evidence + next step |
## Live coding

**Failure dùng để dẫn bài:** Dùng aggregate repository cho dashboard gây N+1 và load object graph lớn

1. Định nghĩa OrderRepository theo domain intent
2. Tạo SalesReportQuery trả projection
3. Đo query count và pagination
4. Viết contract test repository
## Tiêu chí hoàn thành

- [ ] Định nghĩa OrderRepository theo domain intent.
- [ ] Tạo SalesReportQuery trả projection.
- [ ] Đo query count và pagination.
- [ ] Nhóm bàn giao write/read boundary map và query evidence.
- [ ] Có câu trả lời cho trường hợp không áp dụng abstraction.
## Tài liệu lesson

- [Slides của bài Repository Query Object](slides.md): mạch trình bày và sơ đồ dùng khi đứng lớp.
- [Speaker notes](speaker-notes.md): câu hỏi dẫn dắt, lỗi học viên và điểm cần nhấn mạnh cho Repository Query Object.
- [Exercise](exercise.md): bài thực hành tạo evidence cho mục tiêu của lesson.
- [Quiz và đáp án](quiz.md): kiểm tra khả năng giải thích trade-off thay vì nhớ định nghĩa.
- Demo chạy được: `php demo.php` để quan sát luồng chính và failure case của Repository Query Object.
