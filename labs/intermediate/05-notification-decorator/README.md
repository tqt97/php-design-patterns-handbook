# Lab: Decorator cho notification production

## Bối cảnh nghiệp vụ

Notification cần retry, rate limit và audit theo từng provider.

## Mục tiêu học tập

Bạn sẽ thiết kế notification delivery chain gồm audit, rate limit và retry quanh provider adapter. Mục tiêu là phân biệt behavior có thể compose với policy vận hành, kiểm tra retry safety và chứng minh wrapper order không gây gửi trùng notification.
## Sơ đồ định hướng

```mermaid
flowchart LR
  App --> AuditDecorator --> RateLimitDecorator --> RetryDecorator --> Provider
```

## Invariant bắt buộc

- Không retry lỗi validation
- Audit không ghi secret
- Rate limit áp dụng đúng scope

## Nhiệm vụ

1. Chọn thứ tự decorator
2. Test exhausted retries
3. Thêm circuit breaker và giải thích trade-off

## Cách làm gợi ý

1. Chạy acceptance test của **Decorator cho notification production** và ghi lại output trước khi sửa code.
2. Xác định nơi đang bảo vệ `Không retry lỗi validation`; nếu rule nằm ở nhiều chỗ, viết characterization test trước.
3. Tách boundary theo **Decorator**, chỉ tạo abstraction khi nó làm failure hoặc trục thay đổi rõ hơn.
4. Thêm một test phá vỡ invariant và một test mô phỏng failure đặc trưng của `Decorator cho notification production`.
5. Chạy solution sau cùng, so sánh dependency direction và giải thích khác biệt bằng trade-off.
## Chạy bài

```bash
php labs/intermediate/05-notification-decorator/tests/acceptance.php
php labs/intermediate/05-notification-decorator/solution/main.php
```

## Tiêu chí review

- Solution bảo vệ rõ invariant: **Không retry lỗi validation**.
- Contract của **Decorator** dùng vocabulary của `Decorator cho notification production`, không dùng tên chung như `Manager` hoặc `Handler` thiếu ngữ nghĩa.
- Failure path của `Decorator cho notification production` được biểu diễn bằng exception/result có reason cụ thể.
- Test chứng minh behavior và boundary, không khóa chặt thứ tự gọi nội bộ không cần thiết.
- Phần ghi chú nêu được một tình huống mà giải pháp trực tiếp sẽ dễ bảo trì hơn.

## Lời giải định hướng

Mô hình trung tâm là **Notifier và wrapper chain**. Hướng triển khai nên bắt đầu từ invariant và state transition, không bắt đầu bằng việc tạo interface theo tên pattern.

1. logging/metrics/retry là decorator; idempotency ở side-effect boundary.
2. Viết characterization test cho baseline, sau đó thêm contract test cho boundary mới.
3. Mô phỏng failure: wrapper order làm metric đếm retry sai. Test phải kiểm tra state cuối và side effect, không chỉ exception.
4. Ghi lại telemetry tối thiểu: attempt count và wrapper latency.
5. So sánh với giải pháp trực tiếp; chỉ giữ abstraction khi nó làm client biết ít chi tiết hơn hoặc cô lập failure tốt hơn.

### Kết quả mong đợi

- metric đếm logical send và attempts đúng.
- retry không lặp validation/audit ngoài ý muốn.
- decorator chain có contract test.

Chỉ mở [`solution/`](solution/) sau khi bạn đã lưu diagram, test đỏ đầu tiên và giải thích trade-off của bài **05 notification decorator**.
