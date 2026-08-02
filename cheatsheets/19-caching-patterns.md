# Caching Patterns

Chọn cache theo ownership dữ liệu và tolerance với stale data.

## Bảng tra nhanh

| Chủ đề | Hướng dẫn |
| --- | --- |
| **Cache-aside** | App đọc cache, miss thì DB; phổ biến nhưng cần invalidation. |
| **Read-through** | Cache provider load dữ liệu; đơn giản client nhưng tăng coupling provider. |
| **Write-through** | Ghi cache cùng đường ghi; consistency tốt hơn, latency cao hơn. |
| **Write-behind** | Ghi async; throughput cao nhưng rủi ro mất/reorder. |
| **Request memoization** | Chỉ trong một request; tránh duplicate call. |

## Quy trình áp dụng

1. Xác định quyết định liên quan đến **Caching Patterns** và viết một ví dụ cụ thể đang gây khó khăn.
2. Dùng mục **Cache-aside** để kiểm tra trường hợp chính; đối chiếu **Read-through** cho boundary hoặc phương án thay thế.
3. Chuyển lựa chọn thành một test, metric hoặc review question có thể xác minh.
4. Ghi rõ giới hạn của checklist `Caching Patterns` để tránh áp dụng như quy tắc tuyệt đối.

## Lưu ý thực chiến

- Định nghĩa key, TTL, version và invalidation owner.
- Không cache lỗi auth/permission thiếu context.
- Theo dõi hit ratio, stale read và stampede.

## Câu hỏi review

- Trong bối cảnh hiện tại, mục nào của **Caching Patterns** ảnh hưởng trực tiếp đến invariant hoặc user outcome?
- Failure nào trở nên dễ chẩn đoán hơn khi áp dụng hướng dẫn **Cache-aside**?
- Có thể bỏ bớt abstraction hoặc bước vận hành nào mà vẫn giữ đúng contract không?

## Mô hình quyết định: Caching

```mermaid
flowchart LR
    N0[Read] --> N1[CacheHit]
    N1[CacheHit] --> N2[Return; Read]
    N2[Return; Read] --> N3[CacheMiss]
    N3[CacheMiss] --> N4[SourceOfTruth]
    N4[SourceOfTruth] --> N5[PopulateCache]
```

**Điểm kiểm soát thực tiễn:** Cache là projection có thể mất. Source of truth và invalidation owner phải được ghi rõ.

## Evidence tối thiểu

- Đo hit rate, miss penalty và stale-read rate theo key space.
- Test cache miss, invalidation và source fallback.
- Owner cho key schema, TTL và purge khi serializer thay đổi.
- Dashboard chứng minh cache cải thiện SLO chứ không chỉ giảm query count.
