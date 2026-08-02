# Slides — Production Readiness

## Slide 1 — Outcome

- Case: Release payment provider migration
- Invariant nào phải giữ?
- Failure nào đáng sợ nhất?

## Slide 2 — Baseline

- Trình bày code/flow trực tiếp.
- Ưu điểm: ít abstraction, dễ trace.
- Điểm gãy: Readiness checklist.

## Slide 3 — Mental model

```mermaid
flowchart LR
    C[Candidate] --> T[Test evidence]
    T --> M[Migration rehearsal]
    M --> O[Observability ready]
    O --> R[Rollback ready]
    R --> G[Go / no-go]
```

## Slide 4 — Concepts

- Readiness checklist
- Progressive rollout
- SLO/alerts
- Rollback
- Runbook

## Slide 5 — Refactoring journey

1. Go/no-go dựa SLO, migration rehearsal và rollback.
2. Canary theo cohort có kill switch.
3. Runbook chứa query verify và thao tác idempotent.

## Slide 6 — Failure demonstration

- Thực hiện failure injection sát thời điểm rollout và đánh giá go/no-go.
- Mô phỏng rollback khi schema/event đã thay đổi.
- Kiểm tra alert dẫn tới runbook, owner và recovery verification.

## Slide 7 — Production checklist

- Go/no-go dựa SLO, migration rehearsal và rollback.
- Canary theo cohort có kill switch.
- Runbook chứa query verify và thao tác idempotent.
- Thu thập evidence riêng cho **Release and recovery** trước khi rollout.

## Slide 8 — Alternatives và giới hạn

- Baseline đơn giản nhất cho **Release and recovery** là gì?
- Khi nào abstraction hiện tại nên được inline hoặc thay bằng decision table/workflow trực tiếp?
- Rủi ro nào không được pattern này giải quyết?

## Slide 9 — Exercise brief

Mở [exercise.md](exercise.md), xây artifact cho **Release and recovery** và chuẩn bị trình bày: invariant, sơ đồ ownership, failure rehearsal, test evidence và rollback/revisit condition.

## Slide 10 — Exit questions

- Alert nào map trực tiếp tới invariant?
- Rollback có được rehearsal trên dữ liệu đại diện chưa?
- Evidence nào sẽ khiến bạn đổi quyết định cho **Release and recovery**?

