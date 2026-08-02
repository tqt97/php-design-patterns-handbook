# Module 27 — Production: Strategy

## Vì sao bài này tồn tại?

**Pricing rollout** là tình huống độc lập được xây dựng riêng cho Strategy. Bài không lấy từ một source ẩn: bạn tự tạo phiên bản `before` tối thiểu dựa trên mô tả dưới đây, sau đó dùng test để chứng minh refactor không đổi behavior. Bài Production giả định hệ thống đã chạy thật. Ngoài cấu trúc code, lời giải phải xử lý migration, failure, idempotency hoặc observability phù hợp với use case.

## Câu chuyện nghiệp vụ

Hệ thống cần xử lý **Pricing rollout**. `PricingRolloutService` phải hỗ trợ legacy/candidate policy theo tenant và shadow comparison; một `switch` trung tâm không lưu policy version và khó rollback an toàn.

Invariant trung tâm của bài **Strategy** là:

> **cùng input và policy version cho cùng kết quả.**

Ở cấp Production, **Strategy** phải bảo vệ invariant dưới retry/concurrency hoặc partial failure, đồng thời có migration seam, telemetry, rollback trigger và cleanup condition sau rollout.

Failure bắt buộc phải được mô hình hóa:

> **version drift hoặc fallback sai.**

## Trạng thái code ban đầu

```php
final class PricingRolloutService
{
    public function handle(array $input): mixed
    {
        // validation chung, lựa chọn biến thể và side effect
        // đang bị trộn trong một method.
        throw new RuntimeException('Hãy dựng phiên bản before phù hợp đề bài.');
    }
}
```

Đây là skeleton định hướng, không phải lời giải. Hãy thêm dữ liệu và behavior nhỏ nhất đủ tái hiện pain point của **Pricing rollout**.

## Mô hình thiết kế cần hướng tới

```mermaid
sequenceDiagram
    participant C as Pricing API
    participant R as PricingRolloutService
    participant S as TenantPolicySelector
    participant L as LegacyPricing
    participant N as CandidatePricing
    participant M as ComparisonMetrics
    C->>R: quote(tenant, cart)
    R->>S: policyFor(tenant, cohort)
    S-->>R: active + shadow policy
    par serving path
      R->>L: calculate(cart)
    and shadow path
      R->>N: calculate(cart)
    end
    R->>M: record difference and policy version
    R-->>C: served quote
```

Thiết kế production tách **selection**, **serving policy** và **shadow policy**. Mỗi kết quả mang policy version để rollback và điều tra chênh lệch; candidate không được tạo side effect trong shadow mode.

## Nhiệm vụ

1. Khóa behavior hiện tại của **Pricing rollout** bằng characterization test và log một trace hoàn chỉnh.
2. Xác định source of truth, transaction boundary và side effect bên ngoài quanh `PricingPolicy`.
3. Tạo một migration seam để chạy song song implementation cũ/mới; so sánh kết quả trước khi chuyển traffic.
4. Mô phỏng failure **version drift hoặc fallback sai** và chứng minh retry/replay không phá invariant.
5. Bổ sung shadow compare, canary và rollback; định nghĩa metric, alert và rollback trigger.
6. Viết ADR ghi rõ evidence, phương án baseline, cleanup condition và người sở hữu runbook.

## Dữ liệu thử tối thiểu

- Hai scenario hợp lệ đại diện cho hai biến thể khác nhau.
- Một boundary value liên quan trực tiếp tới **cùng input và policy version cho cùng kết quả**.
- Một scenario tạo ra **version drift hoặc fallback sai**.
- Một operation lặp lại và một scenario concurrent/replay.

## Gợi ý triển khai

- Bắt đầu bằng behavior và invariant; chỉ tạo abstraction sau khi xác định đúng trục thay đổi.
- Đặt tên method theo domain của **Pricing rollout**, tránh `execute(mixed $data)` hoặc `handleAnything()`.
- Tách selection/wiring khỏi business calculation; composition root có thể biết concrete class nhưng use case không nên biết.
- Đừng nuốt exception. Map lỗi kỹ thuật thành error có ý nghĩa và giữ nguyên cause để điều tra.
- Nếu **switch nhỏ với hai nhánh ổn định** vẫn rõ ràng hơn và thay đổi chưa có thật, hãy ghi kết luận “chưa cần pattern” trong ADR.

## Test bắt buộc

- Replay cùng operation không tạo kết quả thứ hai và vẫn giữ **cùng input và policy version cho cùng kết quả**.
- Concurrency test tại boundary nơi **version drift hoặc fallback sai** có thể xảy ra.
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

- [ ] Tên class/method phản ánh đúng **Pricing rollout**.
- [ ] Invariant **cùng input và policy version cho cùng kết quả** có test tự động.
- [ ] Failure **version drift hoặc fallback sai** có outcome xác định.
- [ ] Client biết ít concrete detail hơn sau refactor.
- [ ] Diagram, code và test mô tả cùng một boundary.
- [ ] Có giải thích khi nào **switch nhỏ với hai nhánh ổn định** tốt hơn.
- [ ] Có migration, rollback, metric và runbook.

## Câu hỏi design review

1. Trục thay đổi thật sự của **Pricing rollout** là gì, và `PricingPolicy` cô lập nó ở đâu?
2. Invariant **cùng input và policy version cho cùng kết quả** được enforce ở layer nào? Có đường đi nào bypass không?
3. Khi **version drift hoặc fallback sai** xảy ra, client nhận error gì và operator nhìn thấy evidence nào?
4. Pattern này làm dependency graph tốt hơn ở điểm nào, và tạo thêm chi phí gì?
5. Dấu hiệu nào cho biết nên quay lại phương án **switch nhỏ với hai nhánh ổn định**?

## Lời giải tham khảo

Với **Pricing rollout**, hãy hoàn thành test và tự vẽ dependency trước/sau rồi mới mở [`SOLUTION.md`](SOLUTION.md). Khi đối chiếu, ưu tiên invariant, failure semantics và khả năng mở rộng của Strategy thay vì đếm class.
