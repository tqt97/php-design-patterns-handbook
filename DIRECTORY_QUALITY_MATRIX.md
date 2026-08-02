# Ma trận chất lượng theo thư mục

Tài liệu này định nghĩa mục tiêu, artifact bắt buộc và tiêu chí kiểm tra cho từng vùng của repository.

| Thư mục | Mục tiêu | Artifact bắt buộc | Tiêu chí đạt |
|---|---|---|---|
| `docs/00-foundations` | Nền tảng suy luận thiết kế | mental model, PHP example, exercise | giải thích được dependency/change force |
| `docs/01-03-*` | 23 GoF patterns | before/after, UML, test, trade-off | participant đúng pattern, không diagram generic |
| `docs/04-enterprise-patterns` | Boundary ứng dụng/dữ liệu | source link, transaction/failure model | phân biệt write/read/persistence rõ |
| `docs/05-laravel-patterns` | Mapping pattern vào Laravel | lifecycle, wiring, production checklist | domain không phụ thuộc framework |
| `docs/08-interactive` | Luyện quyết định | scenario, constraints, hints, deliverable | người học phải đưa ra decision có evidence |
| `docs/09-expert-practice` | Năng lực Senior/Lead | migration, failure, measurement, rubric | quyết định có verification/rollback |
| `examples` | Minh họa gần gũi | before/after/test/README | behavior tương đương, observation riêng |
| `exercises` | Bài tập dài | brief, diagram, tests, solution | độc lập, không gần-duplicate |
| `kata` | Refactor ngắn | README, starter, solution | hoàn thành trong timebox, intent rõ |
| `labs` | Workshop | starter, acceptance, solution, rubric | chạy được và có failure path |
| `playground` | Thử nghiệm quan sát được | CLI/code, expected output, diagram | flow đúng pattern × domain |
| `production` | Thiết kế vận hành | invariant, failure, metric, runbook | source of truth và recovery rõ |
| `handbook` | Kiến thức kiến trúc | problem, concepts, mental model, practice | đủ chiều sâu và không dùng template chung |
| `interviews` | Đánh giá năng lực | question, answer, tip, deep-dive | trả lời có trade-off/evidence |
| `training` | Giáo án đứng lớp | slides, notes, demo, exercise, quiz | full flow và demo chạy được |
| `src` | Mã reusable | strict types, API nhỏ, tests | source map và smoke test pass |
| `decisions` | Quyết định kiến trúc | context, options, decision, verification | có revisit criteria |
| `benchmarks` | Thí nghiệm hiệu năng | same checksum, warm-up, rounds | không suy diễn kiến trúc từ microbenchmark |

## Quy trình review một file

1. Đối chiếu tên file, H1 và mục tiêu.
2. Xác định audience và prerequisite.
3. Kiểm tra factual/technical correctness.
4. Kiểm tra ví dụ, diagram và link.
5. So sánh với file cùng thư mục để phát hiện lặp.
6. Kiểm tra bài tập/lời giải/test nếu có.
7. Ghi trade-off, failure và trường hợp không áp dụng.
8. Chạy quality gate liên quan.
