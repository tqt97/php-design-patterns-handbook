# Lời giải: Notification Decorator

## Kết luận thiết kế

Bài giải sử dụng **Decorator** để giải quyết đúng change axis của lab. Ghép validation, idempotency, retry và logging quanh channel. Wrapper order được chọn để invalid input dừng sớm, duplicate không gọi provider và retry chỉ áp dụng temporary error.

## Mô hình lời giải

```mermaid
flowchart LR
    App --> Validate[Validation]
    Validate --> Dedupe[Idempotency]
    Dedupe --> Retry[Retry]
    Retry --> Log[Logging]
    Log --> Channel[Provider Channel]
```

## Invariant phải giữ

Một logical notification tạo tối đa một provider request thành công; permanent error không retry; secret không xuất hiện trong log.

## Trình tự triển khai

1. Viết core channel và đo call count baseline.
2. Thêm validation và test invalid input dừng sớm.
3. Thêm idempotency trước retry để giữ logical send.
4. Thêm retry chỉ cho temporary error.
5. Thêm telemetry/redaction và composition test toàn chain.

## Kiểm thử bắt buộc

Call-count tests; ordering test; timeout/permanent error matrix; idempotency conflict test.

## Trade-off

Chain decorator cho phép bật/tắt concern nhưng order trở thành configuration critical. Một ordering sai có thể retry permanent error hoặc log secret, nên composition phải được review như code.

## Production hardening

- Export metric per wrapper: validation reject, dedupe hit, retry count.
- Áp retry budget và jitter.
- Lưu delivery attempt có provider request ID.
- Kiểm tra cấu hình chain khi deploy.

## Khi không nên áp dụng

Không dùng Decorator nếu concern cần phối hợp trạng thái toàn workflow; khi đó application service/pipeline rõ hơn.

## Câu hỏi review

- Idempotency nên bọc trong hay ngoài retry?
- Logging thấy payload trước hay sau redaction?
- Wrapper nào sở hữu timeout?
- Permanent error có bị retry do mapping sai không?

## Review lời giải bằng evidence

Với **Notification Decorator**, reviewer phải lần theo một scenario từ input đến state/side effect cuối, đối chiếu với invariant: **Một logical notification tạo tối đa một provider request thành công; permanent error không retry; secret không xuất hiện trong log.**. Không chấp nhận lời giải chỉ tăng số class nhưng không tạo test tái hiện failure hoặc không làm rõ ownership.


### Checklist cuối

- Logging/redaction/retry order rõ.
- Retry không wrap permanent error.
- Decorator giữ contract base.
