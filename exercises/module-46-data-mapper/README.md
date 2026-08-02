# Module 46 — Production: Data Mapper

## Vì sao bài này tồn tại?

**Legacy schema migration** là tình huống độc lập được xây dựng riêng cho Data Mapper. Bài không lấy từ một source ẩn: bạn tự tạo phiên bản `before` tối thiểu dựa trên mô tả dưới đây, sau đó dùng test để chứng minh refactor không đổi behavior. Bài Production giả định hệ thống đã chạy thật. Ngoài cấu trúc code, lời giải phải xử lý migration, failure, idempotency hoặc observability phù hợp với use case.

## Câu chuyện nghiệp vụ

Hệ thống cần xử lý **Legacy schema migration**. `LegacyMigrationService` đang map field ad-hoc trong batch loop, thiếu reject queue, checkpoint và reconciliation.

Invariant trung tâm của bài **Data Mapper** là:

> **mapping hai chiều bảo toàn money/time/status semantics.**

Ở cấp Production, **Data Mapper** phải bảo vệ invariant dưới retry/concurrency hoặc partial failure, đồng thời có migration seam, telemetry, rollback trigger và cleanup condition sau rollout.

Failure bắt buộc phải được mô hình hóa:

> **null legacy, timezone hoặc enum mapping sai.**

## Trạng thái code ban đầu

```php
final class LegacyMigrationService
{
    public function handle(array $input): mixed
    {
        // validation chung, lựa chọn biến thể và side effect
        // đang bị trộn trong một method.
        throw new RuntimeException('Hãy dựng phiên bản before phù hợp đề bài.');
    }
}
```

Đây là skeleton định hướng, không phải lời giải. Hãy thêm dữ liệu và behavior nhỏ nhất đủ tái hiện pain point của **Legacy schema migration**.

## Mô hình thiết kế cần hướng tới

```mermaid
flowchart LR
    L[(Legacy Schema)] --> R[Legacy Row Reader]
    R --> M[CustomerDataMapper]
    M --> D[Customer Domain Model]
    D --> V[Invariant Validation]
    V --> N[(New Schema)]
    V --> E[Migration Error Queue]
```

Mapper phải mô tả rõ default, null, encoding và identity mapping. Migration cần checkpoint, reject queue và round-trip/sample reconciliation thay vì âm thầm sửa dữ liệu.

## Nhiệm vụ

1. Khóa behavior hiện tại của **Legacy schema migration** bằng characterization test và log một trace hoàn chỉnh.
2. Xác định source of truth, transaction boundary và side effect bên ngoài quanh `LegacyOrderMapper`.
3. Tạo một migration seam để chạy song song implementation cũ/mới; so sánh kết quả trước khi chuyển traffic.
4. Mô phỏng failure **null legacy, timezone hoặc enum mapping sai** và chứng minh retry/replay không phá invariant.
5. Bổ sung golden master mapping và dual-read verification; định nghĩa metric, alert và rollback trigger.
6. Viết ADR ghi rõ evidence, phương án baseline, cleanup condition và người sở hữu runbook.

## Dữ liệu thử tối thiểu

- Hai scenario hợp lệ đại diện cho hai biến thể khác nhau.
- Một boundary value liên quan trực tiếp tới **mapping hai chiều bảo toàn money/time/status semantics**.
- Một scenario tạo ra **null legacy, timezone hoặc enum mapping sai**.
- Một operation lặp lại và một scenario concurrent/replay.

## Gợi ý triển khai

- Bắt đầu bằng behavior và invariant; chỉ tạo abstraction sau khi xác định đúng trục thay đổi.
- Đặt tên method theo domain của **Legacy schema migration**, tránh `execute(mixed $data)` hoặc `handleAnything()`.
- Tách selection/wiring khỏi business calculation; composition root có thể biết concrete class nhưng use case không nên biết.
- Đừng nuốt exception. Map lỗi kỹ thuật thành error có ý nghĩa và giữ nguyên cause để điều tra.
- Nếu **Active Record cho ứng dụng CRUD nhỏ** vẫn rõ ràng hơn và thay đổi chưa có thật, hãy ghi kết luận “chưa cần pattern” trong ADR.

## Test bắt buộc

- Replay cùng operation không tạo kết quả thứ hai và vẫn giữ **mapping hai chiều bảo toàn money/time/status semantics**.
- Concurrency test tại boundary nơi **null legacy, timezone hoặc enum mapping sai** có thể xảy ra.
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

- [ ] Tên class/method phản ánh đúng **Legacy schema migration**.
- [ ] Invariant **mapping hai chiều bảo toàn money/time/status semantics** có test tự động.
- [ ] Failure **null legacy, timezone hoặc enum mapping sai** có outcome xác định.
- [ ] Client biết ít concrete detail hơn sau refactor.
- [ ] Diagram, code và test mô tả cùng một boundary.
- [ ] Có giải thích khi nào **Active Record cho ứng dụng CRUD nhỏ** tốt hơn.
- [ ] Có migration, rollback, metric và runbook.

## Câu hỏi design review

1. Trục thay đổi thật sự của **Legacy schema migration** là gì, và `LegacyOrderMapper` cô lập nó ở đâu?
2. Invariant **mapping hai chiều bảo toàn money/time/status semantics** được enforce ở layer nào? Có đường đi nào bypass không?
3. Khi **null legacy, timezone hoặc enum mapping sai** xảy ra, client nhận error gì và operator nhìn thấy evidence nào?
4. Pattern này làm dependency graph tốt hơn ở điểm nào, và tạo thêm chi phí gì?
5. Dấu hiệu nào cho biết nên quay lại phương án **Active Record cho ứng dụng CRUD nhỏ**?

## Lời giải tham khảo

Với **Legacy schema migration**, hãy hoàn thành test và tự vẽ dependency trước/sau rồi mới mở [`SOLUTION.md`](SOLUTION.md). Khi đối chiếu, ưu tiên invariant, failure semantics và khả năng mở rộng của Data Mapper thay vì đếm class.
