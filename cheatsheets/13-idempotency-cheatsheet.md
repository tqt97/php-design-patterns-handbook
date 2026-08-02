# Idempotency — Cheatsheet

Bảo đảm cùng một logical request không tạo side effect lặp khi client hoặc broker retry.

## Bảng tra nhanh

| Chủ đề | Hướng dẫn |
| --- | --- |
| **Key** | Ổn định theo logical operation; không tái sử dụng cho payload khác. |
| **Payload hash** | Phát hiện cùng key nhưng nội dung khác. |
| **Stored result** | Trả lại response cũ hoặc trạng thái processing/completed. |
| **Concurrency** | Unique constraint hoặc compare-and-set; in-memory check không đủ. |
| **Expiry** | Phù hợp retry window và yêu cầu audit. |

## Quy trình áp dụng

1. Xác định quyết định liên quan đến **Idempotency — Cheatsheet** và viết một ví dụ cụ thể đang gây khó khăn.
2. Dùng mục **Key** để kiểm tra trường hợp chính; đối chiếu **Payload hash** cho boundary hoặc phương án thay thế.
3. Chuyển lựa chọn thành một test, metric hoặc review question có thể xác minh.
4. Ghi rõ giới hạn của checklist `Idempotency — Cheatsheet` để tránh áp dụng như quy tắc tuyệt đối.

## Lưu ý thực chiến

- Đặt key ở boundary nhận request/message.
- Side effect phải nằm sau idempotency claim atomically.
- Theo dõi conflict, duplicate hit và stale processing.

## Câu hỏi review

- Trong bối cảnh hiện tại, mục nào của **Idempotency — Cheatsheet** ảnh hưởng trực tiếp đến invariant hoặc user outcome?
- Failure nào trở nên dễ chẩn đoán hơn khi áp dụng hướng dẫn **Key**?
- Có thể bỏ bớt abstraction hoặc bước vận hành nào mà vẫn giữ đúng contract không?

## Mô hình quyết định: Idempotency

```mermaid
flowchart LR
    N0[RequestKey] --> N1[LookupRecord]
    N1[LookupRecord] --> N2[SamePayload]
    N2[SamePayload] --> N3[ReturnStoredResult]
```

**Điểm kiểm soát thực tiễn:** Cùng key nhưng payload khác phải bị từ chối; đừng âm thầm trả kết quả cũ.

## Evidence tối thiểu

- Test cùng key/cùng payload trả đúng stored result.
- Test cùng key/khác payload bị conflict thay vì dùng nhầm kết quả cũ.
- Metric duplicate request, conflict và record retention.
- Quy tắc cleanup không xóa record trước cửa sổ retry của client/provider.
