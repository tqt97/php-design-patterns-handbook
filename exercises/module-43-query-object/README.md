# Module 43 — Production: Query Object

## Vì sao bài này tồn tại?

**Analytics read model** là tình huống độc lập được xây dựng riêng cho Query Object. Bài không lấy từ một source ẩn: bạn tự tạo phiên bản `before` tối thiểu dựa trên mô tả dưới đây, sau đó dùng test để chứng minh refactor không đổi behavior. Bài Production giả định hệ thống đã chạy thật. Ngoài cấu trúc code, lời giải phải xử lý migration, failure, idempotency hoặc observability phù hợp với use case.

## Câu chuyện nghiệp vụ

Hệ thống cần xử lý **Analytics read model**. `AnalyticsService` đang hydrate aggregate cho truy vấn báo cáo và trộn filter/access/projection trong controller.

Invariant trung tâm của bài **Query Object** là:

> **projection versioned và result reproducible.**

Ở cấp Production, **Query Object** phải bảo vệ invariant dưới retry/concurrency hoặc partial failure, đồng thời có migration seam, telemetry, rollback trigger và cleanup condition sau rollout.

Failure bắt buộc phải được mô hình hóa:

> **timeout, stale replica hoặc cursor drift.**

## Trạng thái code ban đầu

```php
final class AnalyticsService
{
    public function handle(array $input): mixed
    {
        // validation chung, lựa chọn biến thể và side effect
        // đang bị trộn trong một method.
        throw new RuntimeException('Hãy dựng phiên bản before phù hợp đề bài.');
    }
}
```

Đây là skeleton định hướng, không phải lời giải. Hãy thêm dữ liệu và behavior nhỏ nhất đủ tái hiện pain point của **Analytics read model**.

## Mô hình thiết kế cần hướng tới

```mermaid
flowchart LR
    C[AnalyticsController] --> Q[RevenueByCohortQuery]
    Q --> V[Validate filters and access scope]
    V --> R[(Analytics Read Store)]
    R --> P[Projected Page/Series]
    P --> C
```

Query Object sở hữu filter semantics, authorization scope, projection và pagination. Nó cần query plan/latency evidence và không hydrate aggregate cho workload analytics.

## Nhiệm vụ

1. Khóa behavior hiện tại của **Analytics read model** bằng characterization test và log một trace hoàn chỉnh.
2. Xác định source of truth, transaction boundary và side effect bên ngoài quanh `SalesReportQuery`.
3. Tạo một migration seam để chạy song song implementation cũ/mới; so sánh kết quả trước khi chuyển traffic.
4. Mô phỏng failure **timeout, stale replica hoặc cursor drift** và chứng minh retry/replay không phá invariant.
5. Bổ sung read replica policy, cursor pagination và query budget; định nghĩa metric, alert và rollback trigger.
6. Viết ADR ghi rõ evidence, phương án baseline, cleanup condition và người sở hữu runbook.

## Dữ liệu thử tối thiểu

- Hai scenario hợp lệ đại diện cho hai biến thể khác nhau.
- Một boundary value liên quan trực tiếp tới **projection versioned và result reproducible**.
- Một scenario tạo ra **timeout, stale replica hoặc cursor drift**.
- Một operation lặp lại và một scenario concurrent/replay.

## Gợi ý triển khai

- Bắt đầu bằng behavior và invariant; chỉ tạo abstraction sau khi xác định đúng trục thay đổi.
- Đặt tên method theo domain của **Analytics read model**, tránh `execute(mixed $data)` hoặc `handleAnything()`.
- Tách selection/wiring khỏi business calculation; composition root có thể biết concrete class nhưng use case không nên biết.
- Đừng nuốt exception. Map lỗi kỹ thuật thành error có ý nghĩa và giữ nguyên cause để điều tra.
- Nếu **query inline ngắn, chỉ dùng một nơi** vẫn rõ ràng hơn và thay đổi chưa có thật, hãy ghi kết luận “chưa cần pattern” trong ADR.

## Test bắt buộc

- Replay cùng operation không tạo kết quả thứ hai và vẫn giữ **projection versioned và result reproducible**.
- Concurrency test tại boundary nơi **timeout, stale replica hoặc cursor drift** có thể xảy ra.
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

- [ ] Tên class/method phản ánh đúng **Analytics read model**.
- [ ] Invariant **projection versioned và result reproducible** có test tự động.
- [ ] Failure **timeout, stale replica hoặc cursor drift** có outcome xác định.
- [ ] Client biết ít concrete detail hơn sau refactor.
- [ ] Diagram, code và test mô tả cùng một boundary.
- [ ] Có giải thích khi nào **query inline ngắn, chỉ dùng một nơi** tốt hơn.
- [ ] Có migration, rollback, metric và runbook.

## Câu hỏi design review

1. Trục thay đổi thật sự của **Analytics read model** là gì, và `SalesReportQuery` cô lập nó ở đâu?
2. Invariant **projection versioned và result reproducible** được enforce ở layer nào? Có đường đi nào bypass không?
3. Khi **timeout, stale replica hoặc cursor drift** xảy ra, client nhận error gì và operator nhìn thấy evidence nào?
4. Pattern này làm dependency graph tốt hơn ở điểm nào, và tạo thêm chi phí gì?
5. Dấu hiệu nào cho biết nên quay lại phương án **query inline ngắn, chỉ dùng một nơi**?

## Lời giải tham khảo

Với **Analytics read model**, hãy hoàn thành test và tự vẽ dependency trước/sau rồi mới mở [`SOLUTION.md`](SOLUTION.md). Khi đối chiếu, ưu tiên invariant, failure semantics và khả năng mở rộng của Query Object thay vì đếm class.
