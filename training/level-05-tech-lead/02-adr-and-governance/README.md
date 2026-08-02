# Adr And Governance

## Bối cảnh thuyết trình

**Choose Query Object over generic repository** là case xuyên suốt. Giảng viên bắt đầu bằng code hoặc flow trực tiếp, cho học viên dự đoán failure rồi mới giới thiệu vocabulary/pattern.

## Kết quả học tập

- ADR context
- Alternatives
- Consequences
- Fitness function
- Exception process
- Giải thích trade-off và chọn baseline đơn giản hơn khi pattern chưa cần thiết.
- Trình bày test, metric hoặc artifact chứng minh thiết kế hoạt động.

## Luồng hệ thống

```mermaid
flowchart LR
    N0[Decision] --> N1[ADR]
    N1[ADR] --> N2[Guardrail]
    N2[Guardrail] --> N3[Revisit]
```

## Agenda 90 phút

| Phút | Hoạt động | Evidence tạo ra |
|---:|---|---|
| 0–8 | Phân tích ADR chỉ ghi “dùng best practice” và guardrail và chốt invariant | Một câu invariant có thể kiểm thử |
| 8–20 | Vẽ current flow và failure | Diagram có source of truth |
| 20–38 | Giải thích concept trọng tâm | ADR hoàn chỉnh, fitness rule và exception record |
| 38–58 | Live coding theo safety net | Commit nhỏ, demo vẫn chạy |
| 58–73 | Failure injection | Log/output chứng minh behavior |
| 73–84 | Pair review và alternative | Trade-off note |
| 84–90 | Trình bày 3 phút | Decision + evidence + next step |
## Live coding

**Failure dùng để dẫn bài:** ADR chỉ ghi “dùng best practice” và guardrail chặn use case hợp lệ

1. Viết context có forces đo được
2. So sánh ít nhất hai alternatives
3. Ghi consequence và revisit trigger
4. Chuyển một constraint thành fitness function
## Tiêu chí hoàn thành

- [ ] Viết context có forces đo được.
- [ ] So sánh ít nhất hai alternatives.
- [ ] Ghi consequence và revisit trigger.
- [ ] Nhóm bàn giao ADR hoàn chỉnh, fitness rule và exception record.
- [ ] Có câu trả lời cho trường hợp không áp dụng abstraction.
## Tài liệu lesson

- [Slides của bài Adr And Governance](slides.md): mạch trình bày và sơ đồ dùng khi đứng lớp.
- [Speaker notes](speaker-notes.md): câu hỏi dẫn dắt, lỗi học viên và điểm cần nhấn mạnh cho Adr And Governance.
- [Exercise](exercise.md): bài thực hành tạo evidence cho mục tiêu của lesson.
- [Quiz và đáp án](quiz.md): kiểm tra khả năng giải thích trade-off thay vì nhớ định nghĩa.
- Demo chạy được: `php demo.php` để quan sát luồng chính và failure case của Adr And Governance.
