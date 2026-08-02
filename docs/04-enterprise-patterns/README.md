# Enterprise Application Patterns

Nhóm Enterprise giải quyết persistence, transaction, application boundary và business rule trong ứng dụng PHP có dữ liệu lâu dài.

## Mục tiêu học tập

- Phân biệt write model, read model, repository, query object và data mapper.
- Chọn transaction boundary, tracking change và consistency phù hợp.
- Tránh generic abstraction chỉ bọc ORM mà không giảm coupling.

## Nội dung

| Tài liệu | Giá trị chính |
|---|---|
| [Repository](01-repository.md) | Học và áp dụng chủ đề **Repository** qua context, trade-off và bài tập liên quan. |
| [Service Layer](02-service-layer.md) | Học và áp dụng chủ đề **Service Layer** qua context, trade-off và bài tập liên quan. |
| [Query Object](03-query-object.md) | Học và áp dụng chủ đề **Query Object** qua context, trade-off và bài tập liên quan. |
| [Specification](04-specification.md) | Học và áp dụng chủ đề **Specification** qua context, trade-off và bài tập liên quan. |
| [Unit of Work](05-unit-of-work.md) | Học và áp dụng chủ đề **Unit of Work** qua context, trade-off và bài tập liên quan. |
| [Data Mapper](06-data-mapper.md) | Học và áp dụng chủ đề **Data Mapper** qua context, trade-off và bài tập liên quan. |
| [Active Record](07-active-record.md) | Học và áp dụng chủ đề **Active Record** qua context, trade-off và bài tập liên quan. |

## Cách học đề xuất

1. Bắt đầu từ transaction boundary và read/write use case của ứng dụng.
2. Đối chiếu Repository, Query Object, Data Mapper và Active Record trên cùng mô hình dữ liệu.
3. Viết integration test cho persistence contract và unit test cho domain rule.
4. Mô phỏng rollback, stale data và query performance.
5. Chỉ thêm abstraction khi nó bảo vệ domain hoặc cô lập change axis thật.

## Tiêu chí hoàn thành

- Phân biệt write model boundary và read model projection.
- Xác định transaction ownership của Service Layer/Unit of Work.
- Test repository semantics, query ordering và rollback.
- Tránh generic repository và service chỉ chuyển tiếp.
