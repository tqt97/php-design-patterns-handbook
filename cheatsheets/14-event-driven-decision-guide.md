# Event-driven Decision Guide

Chọn event khi cần thông báo sự kiện đã xảy ra, không dùng event để che dependency bắt buộc.

## Bảng tra nhanh

| Chủ đề | Hướng dẫn |
| --- | --- |
| **Gọi trực tiếp** | Dependency bắt buộc, cần response đồng bộ, failure phải trả về caller. |
| **Domain event** | Một thay đổi domain đã xảy ra; subscriber trong cùng boundary. |
| **Integration event** | Contract qua bounded context; cần versioning và delivery semantics. |
| **Command qua queue** | Yêu cầu một capability thực hiện hành động; có owner rõ. |

## Quy trình áp dụng

1. Xác định quyết định liên quan đến **Event-driven Decision Guide** và viết một ví dụ cụ thể đang gây khó khăn.
2. Dùng mục **Gọi trực tiếp** để kiểm tra trường hợp chính; đối chiếu **Domain event** cho boundary hoặc phương án thay thế.
3. Chuyển lựa chọn thành một test, metric hoặc review question có thể xác minh.
4. Ghi rõ giới hạn của checklist `Event-driven Decision Guide` để tránh áp dụng như quy tắc tuyệt đối.

## Lưu ý thực chiến

- Đặt tên event ở thì quá khứ.
- Không để event payload là bản sao toàn bộ entity.
- Thiết kế duplicate, ordering, retry, dead-letter và observability trước production.

## Câu hỏi review

- Trong bối cảnh hiện tại, mục nào của **Event-driven Decision Guide** ảnh hưởng trực tiếp đến invariant hoặc user outcome?
- Failure nào trở nên dễ chẩn đoán hơn khi áp dụng hướng dẫn **Gọi trực tiếp**?
- Có thể bỏ bớt abstraction hoặc bước vận hành nào mà vẫn giữ đúng contract không?

## Mô hình quyết định: Event-driven decision

```mermaid
flowchart LR
    N0[BusinessFact] --> N1[Commit]
    N1[Commit] --> N2[Outbox]
    N2[Outbox] --> N3[Broker]
    N3[Broker] --> N4[IdempotentConsumer]
```

**Điểm kiểm soát thực tiễn:** Event không phải lời gọi hàm từ xa. Hãy thiết kế duplicate, ordering và schema evolution.

## Evidence tối thiểu

- Test publisher commit event atomically với state hoặc dùng outbox.
- Consumer test duplicate, out-of-order và schema version cũ.
- Metric queue lag, retry, dead-letter và processing age.
- Runbook replay có giới hạn và không nhân đôi side effect.
