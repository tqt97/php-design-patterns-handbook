# Repository Manifest

Manifest phản ánh trạng thái sau vòng audit nội dung, UML/Mermaid, source–test evidence và enterprise operability mới nhất.

## Thống kê

| Thành phần | Số lượng |
|---|---:|
| Tổng số file | 1.578 |
| Markdown | 960 |
| PHP | 600 |
| README | 494 |
| GoF Pattern articles | 23 |
| Enterprise Pattern articles | 7 |
| Exercise modules | 52 |
| Kata solutions | 204 |
| Playground drills | 108 |
| Flagship playgrounds | 12 |
| Production modules | 54 |
| Training lessons | 15 |

## Artifact thực hành và enterprise

- GoF/Enterprise source có unit test hoặc source-smoke evidence.
- Property-based invariant workbook và deterministic runner.
- Infection mutation-testing baseline.
- Circuit Breaker, Bulkhead, Distributed Bulkhead, Rate Limiter và Bounded Work Queue teaching implementations.
- Failure classification, Retry Policy và deterministic Failure Injector.
- Transactional Outbox, Idempotency Store và Messaging Deduplication Window.
- Dual-run Comparator cho migration rehearsal.
- Architecture fitness audit và topic–code alignment audit.
- Laravel/Symfony source tours theo tag/commit cố định.
- Incident Packet, postmortem sample và Design Evidence Graph.
- 120 playground, 204 kata, 52 exercise module, 19 lab và 15 training lesson.

## Quality commands quan trọng

```bash
composer content-audit
composer cross-file-uniqueness-audit
composer short-duplication-audit
composer artifact-alignment-audit
composer topic-code-alignment-audit
composer production-design-audit
composer playground-design-audit
composer source-map-audit
composer architecture-fitness-audit
composer enterprise-simulation-coverage-audit
composer property-workbook
composer failure-injection-lab
composer source-smoke
```

## Tài liệu điều hướng

- `README.md`: mục tiêu, lộ trình và cách chạy.
- `OVERVIEW.md`: ma trận pattern và learning path.
- `DIRECTORY_QUALITY_MATRIX.md`: tiêu chí chất lượng theo thư mục.
- `SOURCE_ENTERPRISE_REVIEW.md`: review source và giới hạn production hóa.
- `ENTERPRISE_OPERABILITY_MATRIX.md`: pattern → invariant → failure → test → metric → runbook.
- `REVIEW_REPORT.md`: evidence kiểm tra và giới hạn môi trường.
- `AUDIT_CYCLES.md`: 10 vòng audit gần nhất.
- `RELEASE_CHECKLIST.md`: cổng phát hành.
