# Slides — Adr And Governance

## Slide 1 — Outcome

- Case: Choose Query Object over generic repository
- Invariant nào phải giữ?
- Failure nào đáng sợ nhất?

## Slide 2 — Baseline

- Trình bày code/flow trực tiếp.
- Ưu điểm: ít abstraction, dễ trace.
- Điểm gãy: ADR context.

## Slide 3 — Mental model

```mermaid
flowchart LR
    D[Decision] --> ADR[ADR]
    ADR --> G[Automated guardrail]
    G --> X[Exception process]
    X --> R[Scheduled revisit]
```

## Slide 4 — Concepts

- ADR context
- Alternatives
- Consequences
- Fitness function
- Exception process

## Slide 5 — Refactoring journey

1. ADR ghi drivers, decision, consequences và owner.
2. Fitness function tự động hóa guardrail.
3. Exception có expiry và revisit date.

## Slide 6 — Failure demonstration

- Thay một decision driver và xem ADR có trigger reconsideration hay không.
- Biến một constraint thành fitness function chạy trong CI.
- Xác định exception owner, expiration date và evidence đóng quyết định.

## Slide 7 — Production checklist

- ADR ghi drivers, decision, consequences và owner.
- Fitness function tự động hóa guardrail.
- Exception có expiry và revisit date.
- Thu thập evidence riêng cho **Decision lifecycle** trước khi rollout.

## Slide 8 — Alternatives và giới hạn

- Baseline đơn giản nhất cho **Decision lifecycle** là gì?
- Khi nào abstraction hiện tại nên được inline hoặc thay bằng decision table/workflow trực tiếp?
- Rủi ro nào không được pattern này giải quyết?

## Slide 9 — Exercise brief

Mở [exercise.md](exercise.md), xây artifact cho **Decision lifecycle** và chuẩn bị trình bày: invariant, sơ đồ ownership, failure rehearsal, test evidence và rollback/revisit condition.

## Slide 10 — Exit questions

- ADR nào đã stale so với source hiện tại?
- Governance có hỗ trợ delivery hay chỉ thêm thủ tục?
- Evidence nào sẽ khiến bạn đổi quyết định cho **Decision lifecycle**?

