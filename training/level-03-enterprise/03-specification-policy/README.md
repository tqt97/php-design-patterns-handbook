# Specification Policy

## Bối cảnh thuyết trình

**Promotion eligibility** là case xuyên suốt. Giảng viên bắt đầu bằng code hoặc flow trực tiếp, cho học viên dự đoán failure rồi mới giới thiệu vocabulary/pattern.

## Kết quả học tập

- Composable predicate
- Reason code
- Policy selection
- Explainability
- Property test
- Giải thích trade-off và chọn baseline đơn giản hơn khi pattern chưa cần thiết.
- Trình bày test, metric hoặc artifact chứng minh thiết kế hoạt động.

## Luồng hệ thống

```mermaid
flowchart LR
    N0[PromotionService] --> N1[Specifications]
    N1[Specifications] --> N2[Decision]
```

## Agenda 90 phút

| Phút | Hoạt động | Evidence tạo ra |
|---:|---|---|
| 0–8 | Phân tích Promotion áp dụng sai vì rule compose không g và chốt invariant | Một câu invariant có thể kiểm thử |
| 8–20 | Vẽ current flow và failure | Diagram có source of truth |
| 20–38 | Giải thích concept trọng tâm | rule tree, reason-code table và generated cases |
| 38–58 | Live coding theo safety net | Commit nhỏ, demo vẫn chạy |
| 58–73 | Failure injection | Log/output chứng minh behavior |
| 73–84 | Pair review và alternative | Trade-off note |
| 84–90 | Trình bày 3 phút | Decision + evidence + next step |
## Live coding

**Failure dùng để dẫn bài:** Promotion áp dụng sai vì rule compose không giải thích lý do từ chối

1. Tách eligibility predicates thành specifications
2. Compose AND/OR/NOT với short-circuit rõ
3. Trả reason codes
4. Dùng property tests cho boundary amount/date
## Tiêu chí hoàn thành

- [ ] Tách eligibility predicates thành specifications.
- [ ] Compose AND/OR/NOT với short-circuit rõ.
- [ ] Trả reason codes.
- [ ] Nhóm bàn giao rule tree, reason-code table và generated cases.
- [ ] Có câu trả lời cho trường hợp không áp dụng abstraction.
## Tài liệu lesson

- [Slides của bài Specification Policy](slides.md): mạch trình bày và sơ đồ dùng khi đứng lớp.
- [Speaker notes](speaker-notes.md): câu hỏi dẫn dắt, lỗi học viên và điểm cần nhấn mạnh cho Specification Policy.
- [Exercise](exercise.md): bài thực hành tạo evidence cho mục tiêu của lesson.
- [Quiz và đáp án](quiz.md): kiểm tra khả năng giải thích trade-off thay vì nhớ định nghĩa.
- Demo chạy được: `php demo.php` để quan sát luồng chính và failure case của Specification Policy.
