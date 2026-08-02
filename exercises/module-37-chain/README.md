# Module 37 — Production: Chain of Responsibility

## Vì sao bài này tồn tại?

**Fraud decision chain** là tình huống độc lập được xây dựng riêng cho Chain of Responsibility. Bài không lấy từ một source ẩn: bạn tự tạo phiên bản `before` tối thiểu dựa trên mô tả dưới đây, sau đó dùng test để chứng minh refactor không đổi behavior. Bài Production giả định hệ thống đã chạy thật. Ngoài cấu trúc code, lời giải phải xử lý migration, failure, idempotency hoặc observability phù hợp với use case.

## Câu chuyện nghiệp vụ

Hệ thống cần xử lý **Fraud decision chain**. `FraudDecisionService` đang gom nhiều rule vào một method và chỉ trả boolean, thiếu reason/evidence/manual-review outcome.

Invariant trung tâm của bài **Chain of Responsibility** là:

> **quyết định có reason trail và deterministic order.**

Ở cấp Production, **Chain of Responsibility** phải bảo vệ invariant dưới retry/concurrency hoặc partial failure, đồng thời có migration seam, telemetry, rollback trigger và cleanup condition sau rollout.

Failure bắt buộc phải được mô hình hóa:

> **rule conflict hoặc short-circuit sai.**

## Trạng thái code ban đầu

```php
final class FraudDecisionService
{
    public function handle(array $input): mixed
    {
        // validation chung, lựa chọn biến thể và side effect
        // đang bị trộn trong một method.
        throw new RuntimeException('Hãy dựng phiên bản before phù hợp đề bài.');
    }
}
```

Đây là skeleton định hướng, không phải lời giải. Hãy thêm dữ liệu và behavior nhỏ nhất đủ tái hiện pain point của **Fraud decision chain**.

## Mô hình thiết kế cần hướng tới

```mermaid
flowchart LR
    T[Transaction] --> S[SanctionsRule]
    S -->|clear| V[VelocityRule]
    V -->|clear| D[DeviceRiskRule]
    S -->|deny| X[Denied]
    V -->|review| R[Manual Review]
    D -->|approve| A[Approved]
```

Mỗi rule trả decision có reason/evidence, không chỉ boolean. Thứ tự rule, short-circuit và policy version phải audit được; manual review là outcome chính thức.

## Nhiệm vụ

1. Khóa behavior hiện tại của **Fraud decision chain** bằng characterization test và log một trace hoàn chỉnh.
2. Xác định source of truth, transaction boundary và side effect bên ngoài quanh `FraudDecisionChain`.
3. Tạo một migration seam để chạy song song implementation cũ/mới; so sánh kết quả trước khi chuyển traffic.
4. Mô phỏng failure **rule conflict hoặc short-circuit sai** và chứng minh retry/replay không phá invariant.
5. Bổ sung rule versioning, explainability và shadow evaluation; định nghĩa metric, alert và rollback trigger.
6. Viết ADR ghi rõ evidence, phương án baseline, cleanup condition và người sở hữu runbook.

## Dữ liệu thử tối thiểu

- Hai scenario hợp lệ đại diện cho hai biến thể khác nhau.
- Một boundary value liên quan trực tiếp tới **quyết định có reason trail và deterministic order**.
- Một scenario tạo ra **rule conflict hoặc short-circuit sai**.
- Một operation lặp lại và một scenario concurrent/replay.

## Gợi ý triển khai

- Bắt đầu bằng behavior và invariant; chỉ tạo abstraction sau khi xác định đúng trục thay đổi.
- Đặt tên method theo domain của **Fraud decision chain**, tránh `execute(mixed $data)` hoặc `handleAnything()`.
- Tách selection/wiring khỏi business calculation; composition root có thể biết concrete class nhưng use case không nên biết.
- Đừng nuốt exception. Map lỗi kỹ thuật thành error có ý nghĩa và giữ nguyên cause để điều tra.
- Nếu **if/elseif rõ ràng khi chuỗi ngắn cố định** vẫn rõ ràng hơn và thay đổi chưa có thật, hãy ghi kết luận “chưa cần pattern” trong ADR.

## Test bắt buộc

- Replay cùng operation không tạo kết quả thứ hai và vẫn giữ **quyết định có reason trail và deterministic order**.
- Concurrency test tại boundary nơi **rule conflict hoặc short-circuit sai** có thể xảy ra.
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

- [ ] Tên class/method phản ánh đúng **Fraud decision chain**.
- [ ] Invariant **quyết định có reason trail và deterministic order** có test tự động.
- [ ] Failure **rule conflict hoặc short-circuit sai** có outcome xác định.
- [ ] Client biết ít concrete detail hơn sau refactor.
- [ ] Diagram, code và test mô tả cùng một boundary.
- [ ] Có giải thích khi nào **if/elseif rõ ràng khi chuỗi ngắn cố định** tốt hơn.
- [ ] Có migration, rollback, metric và runbook.

## Câu hỏi design review

1. Trục thay đổi thật sự của **Fraud decision chain** là gì, và `FraudDecisionChain` cô lập nó ở đâu?
2. Invariant **quyết định có reason trail và deterministic order** được enforce ở layer nào? Có đường đi nào bypass không?
3. Khi **rule conflict hoặc short-circuit sai** xảy ra, client nhận error gì và operator nhìn thấy evidence nào?
4. Pattern này làm dependency graph tốt hơn ở điểm nào, và tạo thêm chi phí gì?
5. Dấu hiệu nào cho biết nên quay lại phương án **if/elseif rõ ràng khi chuỗi ngắn cố định**?

## Lời giải tham khảo

Với **Fraud decision chain**, hãy hoàn thành test và tự vẽ dependency trước/sau rồi mới mở [`SOLUTION.md`](SOLUTION.md). Khi đối chiếu, ưu tiên invariant, failure semantics và khả năng mở rộng của Chain of Responsibility thay vì đếm class.
