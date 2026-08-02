# Module 29 — Production: Abstract Factory

## Vì sao bài này tồn tại?

**Bộ client theo khu vực** là tình huống độc lập được xây dựng riêng cho Abstract Factory. Bài không lấy từ một source ẩn: bạn tự tạo phiên bản `before` tối thiểu dựa trên mô tả dưới đây, sau đó dùng test để chứng minh refactor không đổi behavior. Bài Production giả định hệ thống đã chạy thật. Ngoài cấu trúc code, lời giải phải xử lý migration, failure, idempotency hoặc observability phù hợp với use case.

## Câu chuyện nghiệp vụ

Hệ thống cần xử lý **Bộ client theo khu vực**. `RegionalClientProvider` đang tạo payment, identity và storage client riêng lẻ, có nguy cơ trộn endpoint/credential giữa region.

Invariant trung tâm của bài **Abstract Factory** là:

> **serializer, signer và endpoint cùng region.**

Ở cấp Production, **Abstract Factory** phải bảo vệ invariant dưới retry/concurrency hoặc partial failure, đồng thời có migration seam, telemetry, rollback trigger và cleanup condition sau rollout.

Failure bắt buộc phải được mô hình hóa:

> **trộn signer/endpoint khác region.**

## Trạng thái code ban đầu

```php
final class RegionalClientProvider
{
    public function handle(array $input): mixed
    {
        // validation chung, lựa chọn biến thể và side effect
        // đang bị trộn trong một method.
        throw new RuntimeException('Hãy dựng phiên bản before phù hợp đề bài.');
    }
}
```

Đây là skeleton định hướng, không phải lời giải. Hãy thêm dữ liệu và behavior nhỏ nhất đủ tái hiện pain point của **Bộ client theo khu vực**.

## Mô hình thiết kế cần hướng tới

```mermaid
classDiagram
    class RegionalClientFactory {
      <<interface>>
      +payments() PaymentClient
      +identity() IdentityClient
      +storage() StorageClient
    }
    class EuClientFactory
    class ApacClientFactory
    RegionalClientFactory <|.. EuClientFactory
    RegionalClientFactory <|.. ApacClientFactory
    RegionalClientFactory --> PaymentClient
    RegionalClientFactory --> IdentityClient
    RegionalClientFactory --> StorageClient
```

Một factory tạo family client cùng region, endpoint, compliance mode và credential scope. Điều này ngăn ghép client EU với storage APAC và cho phép test tính tương thích của cả family.

## Nhiệm vụ

1. Khóa behavior hiện tại của **Bộ client theo khu vực** bằng characterization test và log một trace hoàn chỉnh.
2. Xác định source of truth, transaction boundary và side effect bên ngoài quanh `RegionalClientFactory`.
3. Tạo một migration seam để chạy song song implementation cũ/mới; so sánh kết quả trước khi chuyển traffic.
4. Mô phỏng failure **trộn signer/endpoint khác region** và chứng minh retry/replay không phá invariant.
5. Bổ sung version family, rollout theo region; định nghĩa metric, alert và rollback trigger.
6. Viết ADR ghi rõ evidence, phương án baseline, cleanup condition và người sở hữu runbook.

## Dữ liệu thử tối thiểu

- Hai scenario hợp lệ đại diện cho hai biến thể khác nhau.
- Một boundary value liên quan trực tiếp tới **serializer, signer và endpoint cùng region**.
- Một scenario tạo ra **trộn signer/endpoint khác region**.
- Một operation lặp lại và một scenario concurrent/replay.

## Gợi ý triển khai

- Bắt đầu bằng behavior và invariant; chỉ tạo abstraction sau khi xác định đúng trục thay đổi.
- Đặt tên method theo domain của **Bộ client theo khu vực**, tránh `execute(mixed $data)` hoặc `handleAnything()`.
- Tách selection/wiring khỏi business calculation; composition root có thể biết concrete class nhưng use case không nên biết.
- Đừng nuốt exception. Map lỗi kỹ thuật thành error có ý nghĩa và giữ nguyên cause để điều tra.
- Nếu **tạo object trực tiếp khi không có family invariant** vẫn rõ ràng hơn và thay đổi chưa có thật, hãy ghi kết luận “chưa cần pattern” trong ADR.

## Test bắt buộc

- Replay cùng operation không tạo kết quả thứ hai và vẫn giữ **serializer, signer và endpoint cùng region**.
- Concurrency test tại boundary nơi **trộn signer/endpoint khác region** có thể xảy ra.
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

- [ ] Tên class/method phản ánh đúng **Bộ client theo khu vực**.
- [ ] Invariant **serializer, signer và endpoint cùng region** có test tự động.
- [ ] Failure **trộn signer/endpoint khác region** có outcome xác định.
- [ ] Client biết ít concrete detail hơn sau refactor.
- [ ] Diagram, code và test mô tả cùng một boundary.
- [ ] Có giải thích khi nào **tạo object trực tiếp khi không có family invariant** tốt hơn.
- [ ] Có migration, rollback, metric và runbook.

## Câu hỏi design review

1. Trục thay đổi thật sự của **Bộ client theo khu vực** là gì, và `RegionalClientFactory` cô lập nó ở đâu?
2. Invariant **serializer, signer và endpoint cùng region** được enforce ở layer nào? Có đường đi nào bypass không?
3. Khi **trộn signer/endpoint khác region** xảy ra, client nhận error gì và operator nhìn thấy evidence nào?
4. Pattern này làm dependency graph tốt hơn ở điểm nào, và tạo thêm chi phí gì?
5. Dấu hiệu nào cho biết nên quay lại phương án **tạo object trực tiếp khi không có family invariant**?

## Lời giải tham khảo

Với **Bộ client theo khu vực**, hãy hoàn thành test và tự vẽ dependency trước/sau rồi mới mở [`SOLUTION.md`](SOLUTION.md). Khi đối chiếu, ưu tiên invariant, failure semantics và khả năng mở rộng của Abstract Factory thay vì đếm class.
