# Module 36 — Production: Proxy

## Vì sao bài này tồn tại?

**Remote service proxy** là tình huống độc lập được xây dựng riêng cho Proxy. Bài không lấy từ một source ẩn: bạn tự tạo phiên bản `before` tối thiểu dựa trên mô tả dưới đây, sau đó dùng test để chứng minh refactor không đổi behavior. Bài Production giả định hệ thống đã chạy thật. Ngoài cấu trúc code, lời giải phải xử lý migration, failure, idempotency hoặc observability phù hợp với use case.

## Câu chuyện nghiệp vụ

Hệ thống cần xử lý **Remote service proxy**. `PricingClient` đang để caller tự cache/fallback nên cache key, TTL và stale semantics không thống nhất.

Invariant trung tâm của bài **Proxy** là:

> **authorization, timeout và cache giữ đúng scope.**

Ở cấp Production, **Proxy** phải bảo vệ invariant dưới retry/concurrency hoặc partial failure, đồng thời có migration seam, telemetry, rollback trigger và cleanup condition sau rollout.

Failure bắt buộc phải được mô hình hóa:

> **cache poisoning hoặc stale authorization.**

## Trạng thái code ban đầu

```php
final class PricingClient
{
    public function handle(array $input): mixed
    {
        // validation chung, lựa chọn biến thể và side effect
        // đang bị trộn trong một method.
        throw new RuntimeException('Hãy dựng phiên bản before phù hợp đề bài.');
    }
}
```

Đây là skeleton định hướng, không phải lời giải. Hãy thêm dữ liệu và behavior nhỏ nhất đủ tái hiện pain point của **Remote service proxy**.

## Mô hình thiết kế cần hướng tới

```mermaid
sequenceDiagram
    participant C as Checkout
    participant P as CachedPricingProxy
    participant K as Cache
    participant R as RemotePricingClient
    C->>P: price(product, context)
    P->>K: get(cacheKey, version)
    alt hit
      K-->>P: price
    else miss
      P->>R: fetch price
      R-->>P: price + version
      P->>K: put with ttl/version
    end
    P-->>C: price
```

Proxy giữ contract remote service nhưng thêm cache/access control. Cache key phải bao gồm tenant, currency và policy version; stale/fallback semantics phải rõ ràng.

## Nhiệm vụ

1. Khóa behavior hiện tại của **Remote service proxy** bằng characterization test và log một trace hoàn chỉnh.
2. Xác định source of truth, transaction boundary và side effect bên ngoài quanh `PricingClient`.
3. Tạo một migration seam để chạy song song implementation cũ/mới; so sánh kết quả trước khi chuyển traffic.
4. Mô phỏng failure **cache poisoning hoặc stale authorization** và chứng minh retry/replay không phá invariant.
5. Bổ sung tenant-aware cache, bulkhead và access audit; định nghĩa metric, alert và rollback trigger.
6. Viết ADR ghi rõ evidence, phương án baseline, cleanup condition và người sở hữu runbook.

## Dữ liệu thử tối thiểu

- Hai scenario hợp lệ đại diện cho hai biến thể khác nhau.
- Một boundary value liên quan trực tiếp tới **authorization, timeout và cache giữ đúng scope**.
- Một scenario tạo ra **cache poisoning hoặc stale authorization**.
- Một operation lặp lại và một scenario concurrent/replay.

## Gợi ý triển khai

- Bắt đầu bằng behavior và invariant; chỉ tạo abstraction sau khi xác định đúng trục thay đổi.
- Đặt tên method theo domain của **Remote service proxy**, tránh `execute(mixed $data)` hoặc `handleAnything()`.
- Tách selection/wiring khỏi business calculation; composition root có thể biết concrete class nhưng use case không nên biết.
- Đừng nuốt exception. Map lỗi kỹ thuật thành error có ý nghĩa và giữ nguyên cause để điều tra.
- Nếu **check quyền trực tiếp khi chỉ một call site** vẫn rõ ràng hơn và thay đổi chưa có thật, hãy ghi kết luận “chưa cần pattern” trong ADR.

## Test bắt buộc

- Replay cùng operation không tạo kết quả thứ hai và vẫn giữ **authorization, timeout và cache giữ đúng scope**.
- Concurrency test tại boundary nơi **cache poisoning hoặc stale authorization** có thể xảy ra.
- Migration test so sánh old/new implementation trên cùng fixture hoặc shadow traffic.
- Telemetry test/assertion cho correlation ID, error class và decision version.

## Deliverable

```text
solution/
├── before.php
├── after.php
├── tests/
│   ├── CharacterizationTest.php
│   ├── ContractOrBehaviorTest.php
│   └── FailurePathTest.php
└── ADR.md
```

Bài Production cần thêm migration.md, dashboard.md và runbook.md.

## Tiêu chí tự chấm

- [ ] Tên class/method phản ánh đúng **Remote service proxy**.
- [ ] Invariant **authorization, timeout và cache giữ đúng scope** có test tự động.
- [ ] Failure **cache poisoning hoặc stale authorization** có outcome xác định.
- [ ] Client biết ít concrete detail hơn sau refactor.
- [ ] Diagram, code và test mô tả cùng một boundary.
- [ ] Có giải thích khi nào **check quyền trực tiếp khi chỉ một call site** tốt hơn.
- [ ] Có migration, rollback, metric và runbook.

## Câu hỏi design review

1. Trục thay đổi thật sự của **Remote service proxy** là gì, và `PricingClient` cô lập nó ở đâu?
2. Invariant **authorization, timeout và cache giữ đúng scope** được enforce ở layer nào? Có đường đi nào bypass không?
3. Khi **cache poisoning hoặc stale authorization** xảy ra, client nhận error gì và operator nhìn thấy evidence nào?
4. Pattern này làm dependency graph tốt hơn ở điểm nào, và tạo thêm chi phí gì?
5. Dấu hiệu nào cho biết nên quay lại phương án **check quyền trực tiếp khi chỉ một call site**?

## Lời giải tham khảo

Với **Remote service proxy**, hãy hoàn thành test và tự vẽ dependency trước/sau rồi mới mở [`SOLUTION.md`](SOLUTION.md). Khi đối chiếu, ưu tiên invariant, failure semantics và khả năng mở rộng của Proxy thay vì đếm class.
