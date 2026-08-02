# Unit Of Work Outbox

## Bối cảnh thuyết trình

**Atomic order and event persistence** là case xuyên suốt. Giảng viên bắt đầu bằng code hoặc flow trực tiếp, cho học viên dự đoán failure rồi mới giới thiệu vocabulary/pattern.

## Kết quả học tập

- Transaction boundary
- Unit of Work
- Outbox
- At-least-once
- Inbox dedupe
- Giải thích trade-off và chọn baseline đơn giản hơn khi pattern chưa cần thiết.
- Trình bày test, metric hoặc artifact chứng minh thiết kế hoạt động.

## Luồng hệ thống

```mermaid
flowchart LR
    N0[UseCase] --> N1[UoW]
    N1[UoW] --> N2[State+Outbox]
    N2[State+Outbox] --> N3[Publisher]
```

## Agenda 90 phút

| Phút | Hoạt động | Evidence tạo ra |
|---:|---|---|
| 0–8 | Phân tích Order commit thành công nhưng event không pub và chốt invariant | Một câu invariant có thể kiểm thử |
| 8–20 | Vẽ current flow và failure | Diagram có source of truth |
| 20–38 | Giải thích concept trọng tâm | crash matrix, outbox sequence và reconciliation query |
| 38–58 | Live coding theo safety net | Commit nhỏ, demo vẫn chạy |
| 58–73 | Failure injection | Log/output chứng minh behavior |
| 73–84 | Pair review và alternative | Trade-off note |
| 84–90 | Trình bày 3 phút | Decision + evidence + next step |
## Live coding

**Failure dùng để dẫn bài:** Order commit thành công nhưng event không publish, hoặc publish lặp sau crash

1. Xác định transaction boundary của order + outbox
2. Mô phỏng rollback UnitOfWork
3. Viết publisher mark-after-send
4. Thiết kế inbox dedupe cho consumer
## Tiêu chí hoàn thành

- [ ] Xác định transaction boundary của order + outbox.
- [ ] Mô phỏng rollback UnitOfWork.
- [ ] Viết publisher mark-after-send.
- [ ] Nhóm bàn giao crash matrix, outbox sequence và reconciliation query.
- [ ] Có câu trả lời cho trường hợp không áp dụng abstraction.
## Tài liệu lesson

- [Slides của bài Unit Of Work Outbox](slides.md): mạch trình bày và sơ đồ dùng khi đứng lớp.
- [Speaker notes](speaker-notes.md): câu hỏi dẫn dắt, lỗi học viên và điểm cần nhấn mạnh cho Unit Of Work Outbox.
- [Exercise](exercise.md): bài thực hành tạo evidence cho mục tiêu của lesson.
- [Quiz và đáp án](quiz.md): kiểm tra khả năng giải thích trade-off thay vì nhớ định nghĩa.
- Demo chạy được: `php demo.php` để quan sát luồng chính và failure case của Unit Of Work Outbox.
