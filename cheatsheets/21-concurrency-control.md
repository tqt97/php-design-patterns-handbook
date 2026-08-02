# Concurrency Control

Chọn optimistic, pessimistic hoặc serialization theo conflict rate và invariant.

## Bảng tra nhanh

| Chủ đề | Hướng dẫn |
| --- | --- |
| **Optimistic locking** | Conflict hiếm; version column; client retry/reload. |
| **Pessimistic locking** | Critical section ngắn, conflict cao; nguy cơ deadlock. |
| **Unique constraint** | Bảo vệ invariant có thể biểu diễn ở DB. |
| **Idempotency key** | Chống duplicate operation, không thay thế locking mọi trường hợp. |
| **Queue partitioning** | Serialize theo aggregate key; cần xử lý hot partition. |

## Quy trình áp dụng

1. Xác định quyết định liên quan đến **Concurrency Control** và viết một ví dụ cụ thể đang gây khó khăn.
2. Dùng mục **Optimistic locking** để kiểm tra trường hợp chính; đối chiếu **Pessimistic locking** cho boundary hoặc phương án thay thế.
3. Chuyển lựa chọn thành một test, metric hoặc review question có thể xác minh.
4. Ghi rõ giới hạn của checklist `Concurrency Control` để tránh áp dụng như quy tắc tuyệt đối.

## Lưu ý thực chiến

- Mô phỏng race bằng test song song/integration.
- Định nghĩa conflict response rõ.
- Không dùng distributed lock khi DB constraint đủ.

## Câu hỏi review

- Trong bối cảnh hiện tại, mục nào của **Concurrency Control** ảnh hưởng trực tiếp đến invariant hoặc user outcome?
- Failure nào trở nên dễ chẩn đoán hơn khi áp dụng hướng dẫn **Optimistic locking**?
- Có thể bỏ bớt abstraction hoặc bước vận hành nào mà vẫn giữ đúng contract không?

## Mô hình quyết định: Concurrency control

```mermaid
flowchart LR
    N0[ReadVersion] --> N1[Decide]
    N1[Decide] --> N2[ConditionalWrite]
    N2[ConditionalWrite] --> N3[Conflict]
    N3[Conflict] --> N4[ReloadOrReject]
```

**Điểm kiểm soát thực tiễn:** Retry chỉ an toàn khi command idempotent và decision được tính lại trên state mới.

## Evidence tối thiểu

- Integration test tạo hai writer cạnh tranh trên cùng entity.
- Assertion chứng minh invariant giữ đúng sau conflict/retry.
- Metric version conflict, deadlock hoặc lock wait theo operation.
- UX/API contract cho stale write, retry budget và manual recovery.
