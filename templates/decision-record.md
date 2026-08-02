# ADR-XXX: Tên quyết định

- **Trạng thái:** Đề xuất | Chấp nhận | Thay thế | Hủy
- **Ngày:** YYYY-MM-DD
- **Owner:** Team/role
- **Liên quan:** issue, PR, dashboard, runbook

## Bối cảnh

Mô tả vấn đề, phạm vi, hệ thống hiện tại và bằng chứng. Không viết giải pháp ở đây.

## Lực tác động và invariant

- Business invariant.
- Reliability/security/performance constraint.
- Migration và compatibility constraint.

## Các lựa chọn

### Lựa chọn A — Baseline trực tiếp

Ưu, nhược, điều kiện phù hợp.

### Lựa chọn B — Phương án được cân nhắc

Ưu, nhược, failure mode.

### Lựa chọn C — Phương án khác

Ưu, nhược, chi phí vận hành.

## Quyết định

Nêu lựa chọn, phạm vi áp dụng và phần cố ý chưa giải quyết.

## Sơ đồ quyết định

```mermaid
flowchart LR
    C[Context] --> D[Decision]
    D --> E[Expected evidence]
    E --> R[Revisit trigger]
```

## Hệ quả tích cực

## Hệ quả tiêu cực

## Kế hoạch migration và rollback

## Cách kiểm chứng

- Test.
- Metric/SLO.
- Log/trace.
- Review date.

## Tiêu chí xem xét lại

Nêu ngưỡng định lượng hoặc sự kiện khiến ADR phải mở lại.
