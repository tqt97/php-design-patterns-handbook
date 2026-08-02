# Adapter Decorator

## Bối cảnh thuyết trình

**Reliable external notification** là case xuyên suốt. Giảng viên bắt đầu bằng code hoặc flow trực tiếp, cho học viên dự đoán failure rồi mới giới thiệu vocabulary/pattern.

## Kết quả học tập

- Target contract
- Error translation
- Decorator order
- Retry safety
- Contract test
- Giải thích trade-off và chọn baseline đơn giản hơn khi pattern chưa cần thiết.
- Trình bày test, metric hoặc artifact chứng minh thiết kế hoạt động.

## Luồng hệ thống

```mermaid
flowchart LR
    N0[NotificationPort] --> N1[Retry/Logging]
    N1[Retry/Logging] --> N2[ProviderAdapter]
```

## Agenda 90 phút

| Phút | Hoạt động | Evidence tạo ra |
|---:|---|---|
| 0–8 | Phân tích Provider timeout sau khi đã nhận request và w và chốt invariant | Một câu invariant có thể kiểm thử |
| 8–20 | Vẽ current flow và failure | Diagram có source of truth |
| 20–38 | Giải thích concept trọng tâm | wrapper order diagram và provider contract fixtures |
| 38–58 | Live coding theo safety net | Commit nhỏ, demo vẫn chạy |
| 58–73 | Failure injection | Log/output chứng minh behavior |
| 73–84 | Pair review và alternative | Trade-off note |
| 84–90 | Trình bày 3 phút | Decision + evidence + next step |
## Live coding

**Failure dùng để dẫn bài:** Provider timeout sau khi đã nhận request và wrapper retry gửi trùng notification

1. Viết target NotificationPort độc lập SDK
2. Map payload/error trong ProviderAdapter
3. Sắp thứ tự Validation, Idempotency, Retry và Logging decorator
4. Test call count khi timeout
## Tiêu chí hoàn thành

- [ ] Viết target NotificationPort độc lập SDK.
- [ ] Map payload/error trong ProviderAdapter.
- [ ] Sắp thứ tự Validation, Idempotency, Retry và Logging decorator.
- [ ] Nhóm bàn giao wrapper order diagram và provider contract fixtures.
- [ ] Có câu trả lời cho trường hợp không áp dụng abstraction.
## Tài liệu lesson

- [Slides của bài Adapter Decorator](slides.md): mạch trình bày và sơ đồ dùng khi đứng lớp.
- [Speaker notes](speaker-notes.md): câu hỏi dẫn dắt, lỗi học viên và điểm cần nhấn mạnh cho Adapter Decorator.
- [Exercise](exercise.md): bài thực hành tạo evidence cho mục tiêu của lesson.
- [Quiz và đáp án](quiz.md): kiểm tra khả năng giải thích trade-off thay vì nhớ định nghĩa.
- Demo chạy được: `php demo.php` để quan sát luồng chính và failure case của Adapter Decorator.
