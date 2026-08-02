# Module 31 — Production: Adapter

## Vì sao bài này tồn tại?

**Migration nhiều provider** là tình huống độc lập được xây dựng riêng cho Adapter. Bài không lấy từ một source ẩn: bạn tự tạo phiên bản `before` tối thiểu dựa trên mô tả dưới đây, sau đó dùng test để chứng minh refactor không đổi behavior. Bài Production giả định hệ thống đã chạy thật. Ngoài cấu trúc code, lời giải phải xử lý migration, failure, idempotency hoặc observability phù hợp với use case.

## Câu chuyện nghiệp vụ

Hệ thống cần xử lý **Migration nhiều provider**. `ProviderMigrationService` đang để application hiểu cả contract cũ và mới, khiến dual-run/shadow comparison rò vào business flow.

Invariant trung tâm của bài **Adapter** là:

> **mọi provider map về cùng result/error taxonomy.**

Ở cấp Production, **Adapter** phải bảo vệ invariant dưới retry/concurrency hoặc partial failure, đồng thời có migration seam, telemetry, rollback trigger và cleanup condition sau rollout.

Failure bắt buộc phải được mô hình hóa:

> **provider trả success muộn sau timeout.**

## Trạng thái code ban đầu

```php
final class ProviderMigrationService
{
    public function handle(array $input): mixed
    {
        // validation chung, lựa chọn biến thể và side effect
        // đang bị trộn trong một method.
        throw new RuntimeException('Hãy dựng phiên bản before phù hợp đề bài.');
    }
}
```

Đây là skeleton định hướng, không phải lời giải. Hãy thêm dữ liệu và behavior nhỏ nhất đủ tái hiện pain point của **Migration nhiều provider**.

## Mô hình thiết kế cần hướng tới

```mermaid
sequenceDiagram
    participant A as Application Service
    participant P as ProviderPort
    participant L as LegacyProviderAdapter
    participant N as NewProviderAdapter
    participant C as Comparison Store
    A->>P: execute canonical request
    P->>L: legacy call (serving)
    par optional shadow
      P->>N: new provider dry-run/read-only call
    end
    L-->>P: canonical result
    N-->>C: mapped shadow result
    P-->>A: canonical result/error
```

Mỗi adapter dịch request, response và error taxonomy về contract chuẩn. Migration hỗ trợ shadow/dual-read nhưng phải ngăn duplicate side effect bằng operation id và capability flags.

## Nhiệm vụ

1. Khóa behavior hiện tại của **Migration nhiều provider** bằng characterization test và log một trace hoàn chỉnh.
2. Xác định source of truth, transaction boundary và side effect bên ngoài quanh `ProviderAdapter`.
3. Tạo một migration seam để chạy song song implementation cũ/mới; so sánh kết quả trước khi chuyển traffic.
4. Mô phỏng failure **provider trả success muộn sau timeout** và chứng minh retry/replay không phá invariant.
5. Bổ sung contract test, idempotency và provider telemetry; định nghĩa metric, alert và rollback trigger.
6. Viết ADR ghi rõ evidence, phương án baseline, cleanup condition và người sở hữu runbook.

## Dữ liệu thử tối thiểu

- Hai scenario hợp lệ đại diện cho hai biến thể khác nhau.
- Một boundary value liên quan trực tiếp tới **mọi provider map về cùng result/error taxonomy**.
- Một scenario tạo ra **provider trả success muộn sau timeout**.
- Một operation lặp lại và một scenario concurrent/replay.

## Gợi ý triển khai

- Bắt đầu bằng behavior và invariant; chỉ tạo abstraction sau khi xác định đúng trục thay đổi.
- Đặt tên method theo domain của **Migration nhiều provider**, tránh `execute(mixed $data)` hoặc `handleAnything()`.
- Tách selection/wiring khỏi business calculation; composition root có thể biết concrete class nhưng use case không nên biết.
- Đừng nuốt exception. Map lỗi kỹ thuật thành error có ý nghĩa và giữ nguyên cause để điều tra.
- Nếu **gọi SDK trực tiếp khi integration dùng một lần** vẫn rõ ràng hơn và thay đổi chưa có thật, hãy ghi kết luận “chưa cần pattern” trong ADR.

## Test bắt buộc

- Replay cùng operation không tạo kết quả thứ hai và vẫn giữ **mọi provider map về cùng result/error taxonomy**.
- Concurrency test tại boundary nơi **provider trả success muộn sau timeout** có thể xảy ra.
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

- [ ] Tên class/method phản ánh đúng **Migration nhiều provider**.
- [ ] Invariant **mọi provider map về cùng result/error taxonomy** có test tự động.
- [ ] Failure **provider trả success muộn sau timeout** có outcome xác định.
- [ ] Client biết ít concrete detail hơn sau refactor.
- [ ] Diagram, code và test mô tả cùng một boundary.
- [ ] Có giải thích khi nào **gọi SDK trực tiếp khi integration dùng một lần** tốt hơn.
- [ ] Có migration, rollback, metric và runbook.

## Câu hỏi design review

1. Trục thay đổi thật sự của **Migration nhiều provider** là gì, và `ProviderAdapter` cô lập nó ở đâu?
2. Invariant **mọi provider map về cùng result/error taxonomy** được enforce ở layer nào? Có đường đi nào bypass không?
3. Khi **provider trả success muộn sau timeout** xảy ra, client nhận error gì và operator nhìn thấy evidence nào?
4. Pattern này làm dependency graph tốt hơn ở điểm nào, và tạo thêm chi phí gì?
5. Dấu hiệu nào cho biết nên quay lại phương án **gọi SDK trực tiếp khi integration dùng một lần**?

## Lời giải tham khảo

Với **Migration nhiều provider**, hãy hoàn thành test và tự vẽ dependency trước/sau rồi mới mở [`SOLUTION.md`](SOLUTION.md). Khi đối chiếu, ưu tiên invariant, failure semantics và khả năng mở rộng của Adapter thay vì đếm class.
