# Microservice Consistency

## Bối cảnh thuyết trình

**Distributed order workflow** là case xuyên suốt. Giảng viên bắt đầu bằng code hoặc flow trực tiếp, cho học viên dự đoán failure rồi mới giới thiệu vocabulary/pattern.

## Kết quả học tập

- Local transaction
- Saga
- Outbox/Inbox
- Idempotency
- Reconciliation
- Giải thích trade-off và chọn baseline đơn giản hơn khi pattern chưa cần thiết.
- Trình bày test, metric hoặc artifact chứng minh thiết kế hoạt động.

## Luồng hệ thống

```mermaid
flowchart LR
    N0[Order] --> N1[Broker]
    N1[Broker] --> N2[Payment/Inventory]
    N2[Payment/Inventory] --> N3[ProcessManager]
```

## Agenda 90 phút

| Phút | Hoạt động | Evidence tạo ra |
|---:|---|---|
| 0–8 | Phân tích Payment confirmed nhưng stock reject; event đ và chốt invariant | Một câu invariant có thể kiểm thử |
| 8–20 | Vẽ current flow và failure | Diagram có source of truth |
| 20–38 | Giải thích concept trọng tâm | saga state machine, message contract và stuck-process dashboard |
| 38–58 | Live coding theo safety net | Commit nhỏ, demo vẫn chạy |
| 58–73 | Failure injection | Log/output chứng minh behavior |
| 73–84 | Pair review và alternative | Trade-off note |
| 84–90 | Trình bày 3 phút | Decision + evidence + next step |
## Live coding

**Failure dùng để dẫn bài:** Payment confirmed nhưng stock reject; event đến lặp và đảo thứ tự

1. Vẽ saga/process-manager states
2. Định nghĩa idempotency key mỗi command
3. Thiết kế compensation và timeout
4. Thêm reconciliation/manual intervention
## Tiêu chí hoàn thành

- [ ] Vẽ saga/process-manager states.
- [ ] Định nghĩa idempotency key mỗi command.
- [ ] Thiết kế compensation và timeout.
- [ ] Nhóm bàn giao saga state machine, message contract và stuck-process dashboard.
- [ ] Có câu trả lời cho trường hợp không áp dụng abstraction.
## Tài liệu lesson

- [Slides của bài Microservice Consistency](slides.md): mạch trình bày và sơ đồ dùng khi đứng lớp.
- [Speaker notes](speaker-notes.md): câu hỏi dẫn dắt, lỗi học viên và điểm cần nhấn mạnh cho Microservice Consistency.
- [Exercise](exercise.md): bài thực hành tạo evidence cho mục tiêu của lesson.
- [Quiz và đáp án](quiz.md): kiểm tra khả năng giải thích trade-off thay vì nhớ định nghĩa.
- Demo chạy được: `php demo.php` để quan sát luồng chính và failure case của Microservice Consistency.
