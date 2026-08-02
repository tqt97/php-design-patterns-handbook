# Module 50 — Production: Job

## Vì sao bài này tồn tại?

**Media processing job** là tình huống độc lập được xây dựng riêng cho Job. Bài không lấy từ một source ẩn: bạn tự tạo phiên bản `before` tối thiểu dựa trên mô tả dưới đây, sau đó dùng test để chứng minh refactor không đổi behavior. Bài Production giả định hệ thống đã chạy thật. Ngoài cấu trúc code, lời giải phải xử lý migration, failure, idempotency hoặc observability phù hợp với use case.

## Câu chuyện nghiệp vụ

Hệ thống cần xử lý **Media processing job**. `MediaJobDispatcher` đang đưa binary/object lớn vào queue và không có claim/checksum/heartbeat cho job dài.

Invariant trung tâm của bài **Job** là:

> **job resumable và resource bounded.**

Ở cấp Production, **Job** phải bảo vệ invariant dưới retry/concurrency hoặc partial failure, đồng thời có migration seam, telemetry, rollback trigger và cleanup condition sau rollout.

Failure bắt buộc phải được mô hình hóa:

> **poison message, timeout hoặc duplicate output.**

## Trạng thái code ban đầu

```php
final class MediaJobDispatcher
{
    public function handle(array $input): mixed
    {
        // validation chung, lựa chọn biến thể và side effect
        // đang bị trộn trong một method.
        throw new RuntimeException('Hãy dựng phiên bản before phù hợp đề bài.');
    }
}
```

Đây là skeleton định hướng, không phải lời giải. Hãy thêm dữ liệu và behavior nhỏ nhất đủ tái hiện pain point của **Media processing job**.

## Mô hình thiết kế cần hướng tới

```mermaid
sequenceDiagram
    participant U as Upload API
    participant Q as Queue
    participant J as TranscodeJob
    participant T as Transcoder
    participant S as MediaStore
    U->>Q: enqueue(assetId, profile, jobVersion)
    Q->>J: deliver
    J->>S: claim asset/profile
    J->>T: transcode(source, profile)
    T-->>J: output/checksum
    J->>S: persist output + status
```

Job dùng asset id thay vì serialize binary. Timeout, heartbeat, retry budget, duplicate execution và poison media cần metric/dead-letter/manual retry.

## Nhiệm vụ

1. Khóa behavior hiện tại của **Media processing job** bằng characterization test và log một trace hoàn chỉnh.
2. Xác định source of truth, transaction boundary và side effect bên ngoài quanh `TranscodeJob`.
3. Tạo một migration seam để chạy song song implementation cũ/mới; so sánh kết quả trước khi chuyển traffic.
4. Mô phỏng failure **poison message, timeout hoặc duplicate output** và chứng minh retry/replay không phá invariant.
5. Bổ sung idempotent artifact key, DLQ và progress checkpoint; định nghĩa metric, alert và rollback trigger.
6. Viết ADR ghi rõ evidence, phương án baseline, cleanup condition và người sở hữu runbook.

## Dữ liệu thử tối thiểu

- Hai scenario hợp lệ đại diện cho hai biến thể khác nhau.
- Một boundary value liên quan trực tiếp tới **job resumable và resource bounded**.
- Một scenario tạo ra **poison message, timeout hoặc duplicate output**.
- Một operation lặp lại và một scenario concurrent/replay.

## Gợi ý triển khai

- Bắt đầu bằng behavior và invariant; chỉ tạo abstraction sau khi xác định đúng trục thay đổi.
- Đặt tên method theo domain của **Media processing job**, tránh `execute(mixed $data)` hoặc `handleAnything()`.
- Tách selection/wiring khỏi business calculation; composition root có thể biết concrete class nhưng use case không nên biết.
- Đừng nuốt exception. Map lỗi kỹ thuật thành error có ý nghĩa và giữ nguyên cause để điều tra.
- Nếu **gọi đồng bộ khi latency nhỏ và cần kết quả ngay** vẫn rõ ràng hơn và thay đổi chưa có thật, hãy ghi kết luận “chưa cần pattern” trong ADR.

## Test bắt buộc

- Replay cùng operation không tạo kết quả thứ hai và vẫn giữ **job resumable và resource bounded**.
- Concurrency test tại boundary nơi **poison message, timeout hoặc duplicate output** có thể xảy ra.
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

- [ ] Tên class/method phản ánh đúng **Media processing job**.
- [ ] Invariant **job resumable và resource bounded** có test tự động.
- [ ] Failure **poison message, timeout hoặc duplicate output** có outcome xác định.
- [ ] Client biết ít concrete detail hơn sau refactor.
- [ ] Diagram, code và test mô tả cùng một boundary.
- [ ] Có giải thích khi nào **gọi đồng bộ khi latency nhỏ và cần kết quả ngay** tốt hơn.
- [ ] Có migration, rollback, metric và runbook.

## Câu hỏi design review

1. Trục thay đổi thật sự của **Media processing job** là gì, và `TranscodeJob` cô lập nó ở đâu?
2. Invariant **job resumable và resource bounded** được enforce ở layer nào? Có đường đi nào bypass không?
3. Khi **poison message, timeout hoặc duplicate output** xảy ra, client nhận error gì và operator nhìn thấy evidence nào?
4. Pattern này làm dependency graph tốt hơn ở điểm nào, và tạo thêm chi phí gì?
5. Dấu hiệu nào cho biết nên quay lại phương án **gọi đồng bộ khi latency nhỏ và cần kết quả ngay**?

## Lời giải tham khảo

Với **Media processing job**, hãy hoàn thành test và tự vẽ dependency trước/sau rồi mới mở [`SOLUTION.md`](SOLUTION.md). Khi đối chiếu, ưu tiên invariant, failure semantics và khả năng mở rộng của Job thay vì đếm class.
