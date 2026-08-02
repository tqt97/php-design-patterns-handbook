# Exercises — Bài tập thiết kế theo cấp độ

Thư mục gồm 52 module độc lập: 26 bài Foundation và 26 bài Production. Mỗi module tự nêu bối cảnh, invariant, failure, diagram, dữ liệu thử, deliverable và lời giải tham khảo; không cần đọc một “đề bài gốc” ở nơi khác.


> Xem [Exercise Design Matrix](EXERCISE_DESIGN_MATRIX.md) để chọn bài theo cấp độ, pattern, domain và loại sơ đồ.

## Cách học

1. Chọn bài Foundation để nhận diện intent và refactor tối thiểu.
2. Tự dựng `before.php`, viết characterization test rồi mới đọc `SOLUTION.md`.
3. Làm bài Production cùng pattern để thêm migration, idempotency, observability và runbook.
4. So sánh evidence của pattern với phương án trực tiếp; không chấm điểm dựa trên số class.

## Bản đồ module

| Foundation | Production | Chủ đề |
|---|---|---|
| [Module 01](module-01-strategy/README.md) | [Module 27](module-27-strategy/README.md) | Strategy |
| [Module 02](module-02-factory/README.md) | [Module 28](module-28-factory/README.md) | Factory Method |
| [Module 03](module-03-abstract-factory/README.md) | [Module 29](module-29-abstract-factory/README.md) | Abstract Factory |
| [Module 04](module-04-builder/README.md) | [Module 30](module-30-builder/README.md) | Builder |
| [Module 05](module-05-adapter/README.md) | [Module 31](module-31-adapter/README.md) | Adapter |
| [Module 06](module-06-bridge/README.md) | [Module 32](module-32-bridge/README.md) | Bridge |
| [Module 07](module-07-composite/README.md) | [Module 33](module-33-composite/README.md) | Composite |
| [Module 08](module-08-decorator/README.md) | [Module 34](module-34-decorator/README.md) | Decorator |
| [Module 09](module-09-facade/README.md) | [Module 35](module-35-facade/README.md) | Facade |
| [Module 10](module-10-proxy/README.md) | [Module 36](module-36-proxy/README.md) | Proxy |
| [Module 11](module-11-chain/README.md) | [Module 37](module-37-chain/README.md) | Chain of Responsibility |
| [Module 12](module-12-command/README.md) | [Module 38](module-38-command/README.md) | Command |
| [Module 13](module-13-observer/README.md) | [Module 39](module-39-observer/README.md) | Observer |
| [Module 14](module-14-state/README.md) | [Module 40](module-40-state/README.md) | State |
| [Module 15](module-15-template-method/README.md) | [Module 41](module-41-template-method/README.md) | Template Method |
| [Module 16](module-16-repository/README.md) | [Module 42](module-42-repository/README.md) | Repository |
| [Module 17](module-17-query-object/README.md) | [Module 43](module-43-query-object/README.md) | Query Object |
| [Module 18](module-18-specification/README.md) | [Module 44](module-44-specification/README.md) | Specification |
| [Module 19](module-19-unit-of-work/README.md) | [Module 45](module-45-unit-of-work/README.md) | Unit of Work |
| [Module 20](module-20-data-mapper/README.md) | [Module 46](module-46-data-mapper/README.md) | Data Mapper |
| [Module 21](module-21-active-record/README.md) | [Module 47](module-47-active-record/README.md) | Active Record |
| [Module 22](module-22-pipeline/README.md) | [Module 48](module-48-pipeline/README.md) | Pipeline |
| [Module 23](module-23-event/README.md) | [Module 49](module-49-event/README.md) | Domain Event |
| [Module 24](module-24-job/README.md) | [Module 50](module-50-job/README.md) | Job |
| [Module 25](module-25-middleware/README.md) | [Module 51](module-51-middleware/README.md) | Middleware |
| [Module 26](module-26-service-layer/README.md) | [Module 52](module-52-service-layer/README.md) | Service Layer |

## Definition of Done

Một bài đạt khi code chạy được, test khóa invariant/failure, diagram khớp dependency thật, ADR nêu trade-off và bài Production có migration/rollback/metric/runbook phù hợp.
