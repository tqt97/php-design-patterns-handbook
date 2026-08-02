# Lời giải: Customer Query Object

## Kết luận thiết kế

Bài giải sử dụng **Query Object** để giải quyết đúng change axis của lab. Đóng gói filter, sort, cursor và projection của màn hình khách hàng trong Query Object. Query trả read model, không trả aggregate hoặc ORM builder cho caller.

## Mô hình lời giải

```mermaid
flowchart LR
    Controller --> Criteria[CustomerSearchCriteria]
    Criteria --> Query[SearchCustomers]
    Query --> DB[(Read Database)]
    DB --> Page[CustomerSummary Page]
    Page --> Controller
```

## Invariant phải giữ

Ordering ổn định; cursor không bỏ/trùng record; filter semantics rõ; projection không kích hoạt lazy loading.

## Trình tự triển khai

1. Viết criteria và projection từ nhu cầu UI.
2. Chụp dataset kỳ vọng cho filter/sort/page.
3. Cài Query Object với ordering deterministic.
4. Thêm cursor/index và đo query plan.
5. Giữ controller chỉ parse input và serialize page.

## Kiểm thử bắt buộc

Integration test với dataset cạnh biên; query-count/plan check; pagination property tests; invalid criteria test.

## Trade-off

Query Object tách read complexity nhưng có thể nở nhiều class theo màn hình. Đây là chi phí chấp nhận được khi projection và performance contract thay đổi độc lập write domain.

## Production hardening

- Theo dõi query latency/p95 và rows examined.
- Version projection nếu client phụ thuộc schema.
- Giới hạn filter/sort để bảo vệ database.
- Xác định consistency của replica/cache.

## Khi không nên áp dụng

Scope nhỏ trên ORM đủ dùng nếu query chỉ có một filter và không có lifecycle/performance contract riêng.

## Câu hỏi review

- Cursor dựa cột nào và có unique tie-breaker không?
- Criteria nào có thể phá index?
- Projection có rò PII không?
- UI thay đổi có ảnh hưởng aggregate contract không?

## Review lời giải bằng evidence

Với **Customer Query Object**, reviewer phải lần theo một scenario từ input đến state/side effect cuối, đối chiếu với invariant: **Ordering ổn định; cursor không bỏ/trùng record; filter semantics rõ; projection không kích hoạt lazy loading.**. Không chấp nhận lời giải chỉ tăng số class nhưng không tạo test tái hiện failure hoặc không làm rõ ownership.


### Checklist cuối

- Search criteria immutable.
- Projection chỉ chứa field cần thiết.
- Pagination ổn định khi data thay đổi.
