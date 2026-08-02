# Production Readiness

## Bối cảnh thuyết trình

**Release payment provider migration** là case xuyên suốt. Giảng viên bắt đầu bằng code hoặc flow trực tiếp, cho học viên dự đoán failure rồi mới giới thiệu vocabulary/pattern.

## Kết quả học tập

- Readiness checklist
- Progressive rollout
- SLO/alerts
- Rollback
- Runbook
- Giải thích trade-off và chọn baseline đơn giản hơn khi pattern chưa cần thiết.
- Trình bày test, metric hoặc artifact chứng minh thiết kế hoạt động.

## Luồng hệ thống

```mermaid
flowchart LR
    N0[Candidate] --> N1[Rehearsal]
    N1[Rehearsal] --> N2[Cohort]
    N2[Cohort] --> N3[Observe]
    N3[Observe] --> N4[Cutover]
```

## Agenda 90 phút

| Phút | Hoạt động | Evidence tạo ra |
|---:|---|---|
| 0–8 | Phân tích Provider migration rollout 100% gây duplicate và chốt invariant | Một câu invariant có thể kiểm thử |
| 8–20 | Vẽ current flow và failure | Diagram có source of truth |
| 20–38 | Giải thích concept trọng tâm | go/no-go packet, rollout timeline và rollback evidence |
| 38–58 | Live coding theo safety net | Commit nhỏ, demo vẫn chạy |
| 58–73 | Failure injection | Log/output chứng minh behavior |
| 73–84 | Pair review và alternative | Trade-off note |
| 84–90 | Trình bày 3 phút | Decision + evidence + next step |
## Live coding

**Failure dùng để dẫn bài:** Provider migration rollout 100% gây duplicate charge và không rollback được

1. Phân loại schema/behavior/infrastructure changes
2. Thiết kế cohort rollout và stop conditions
3. Chuẩn bị dashboards, alerts và runbook
4. Diễn tập rollback/failover
## Tiêu chí hoàn thành

- [ ] Phân loại schema/behavior/infrastructure changes.
- [ ] Thiết kế cohort rollout và stop conditions.
- [ ] Chuẩn bị dashboards, alerts và runbook.
- [ ] Nhóm bàn giao go/no-go packet, rollout timeline và rollback evidence.
- [ ] Có câu trả lời cho trường hợp không áp dụng abstraction.
## Tài liệu lesson

- [Slides của bài Production Readiness](slides.md): mạch trình bày và sơ đồ dùng khi đứng lớp.
- [Speaker notes](speaker-notes.md): câu hỏi dẫn dắt, lỗi học viên và điểm cần nhấn mạnh cho Production Readiness.
- [Exercise](exercise.md): bài thực hành tạo evidence cho mục tiêu của lesson.
- [Quiz và đáp án](quiz.md): kiểm tra khả năng giải thích trade-off thay vì nhớ định nghĩa.
- Demo chạy được: `php demo.php` để quan sát luồng chính và failure case của Production Readiness.
