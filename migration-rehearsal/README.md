# Migration Rehearsal Lab

Lab dùng `DualRunComparator` để chạy implementation cũ và mới trên cùng input, normalize field không mang semantics và ghi diff.

```mermaid
flowchart LR
    IN[Recorded input] --> OLD[Authoritative old path]
    IN --> NEW[Shadow new path]
    OLD --> CMP[Normalizer and comparator]
    NEW --> CMP
    CMP --> METRIC[Diff metric and samples]
    METRIC --> GATE[Cutover or rollback decision]
```

Bài tập:

- Tạo 100 input đã redacted.
- Thêm mismatch critical/non-critical.
- Chặn side effect ở shadow path.
- Đặt cutover threshold và rollback trigger.
- Lưu review packet gồm diff, latency và resource usage.
