# Laravel Patterns

Phần này giải thích các pattern và convention phía sau Laravel, đồng thời chỉ ra ranh giới giữa tận dụng framework và phụ thuộc framework quá mức.

## Mục tiêu học tập

- Hiểu container, provider, facade, event, job, pipeline và middleware theo runtime Laravel.
- Thiết kế application/domain boundary có thể test mà không boot toàn framework khi không cần.
- Phân biệt Eloquent query, Query Object và Repository theo use case.

## Nội dung

| Tài liệu | Giá trị chính |
|---|---|
| [Service Container trong Laravel](01-service-container.md) | Học và áp dụng chủ đề **Service Container trong Laravel** qua context, trade-off và bài tập liên quan. |
| [Service Provider trong Laravel](02-service-provider.md) | Học và áp dụng chủ đề **Service Provider trong Laravel** qua context, trade-off và bài tập liên quan. |
| [Facade trong Laravel](03-facade.md) | Học và áp dụng chủ đề **Facade trong Laravel** qua context, trade-off và bài tập liên quan. |
| [Events trong Laravel](04-events.md) | Học và áp dụng chủ đề **Events trong Laravel** qua context, trade-off và bài tập liên quan. |
| [Jobs trong Laravel](05-jobs.md) | Học và áp dụng chủ đề **Jobs trong Laravel** qua context, trade-off và bài tập liên quan. |
| [Pipeline trong Laravel](06-pipeline.md) | Học và áp dụng chủ đề **Pipeline trong Laravel** qua context, trade-off và bài tập liên quan. |
| [Middleware trong Laravel](07-middleware.md) | Học và áp dụng chủ đề **Middleware trong Laravel** qua context, trade-off và bài tập liên quan. |
| [Repository và Query Object trong Laravel](08-repository-query-object.md) | Học và áp dụng chủ đề **Repository và Query Object trong Laravel** qua context, trade-off và bài tập liên quan. |

## Cách học đề xuất

1. Tách API Laravel khỏi intent pattern: container, event, job, middleware hay query.
2. Trace lifecycle request/worker để hiểu scope, serialization và after-commit.
3. Viết framework test cho wiring và domain test không boot Laravel.
4. Kiểm tra retry, tenant boundary, transaction và queue failure.
5. Đánh giá framework convenience có rò vào core model hay không.

## Tiêu chí hoàn thành

- Giải thích lifecycle và failure semantics của từng Laravel capability.
- Tách domain rule khỏi Facade, Eloquent, Request và Job payload.
- Test wiring, after-commit, retry và serialization.
- Nhận diện repository hoặc service layer thừa trong CRUD đơn giản.
