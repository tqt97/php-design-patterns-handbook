# Playground 29: Observer — Shipping

## Mục tiêu học tập

Chạy một ví dụ nhỏ về **tính phí và ETA theo vùng** và quan sát cách **Observer** giúp tách side effect sau một sự kiện đã xảy ra. Playground này là drill 10–15 phút; flagship playground phù hợp hơn cho luồng nhiều bước.

Sau bài này, người học phải giải thích được **một sự kiện có nhiều phản ứng độc lập** trong bối cảnh shipping, chỉ ra dependency đã bị đảo hoặc che giấu, và nêu trường hợp giải pháp trực tiếp vẫn tốt hơn.

## Bối cảnh và invariant

- **Nghiệp vụ:** carrier, zone, weight và promised date.
- **Invariant:** phí và SLA phải nhất quán với tuyến giao hàng.
- **Change axis:** thêm subscriber mà không sửa publisher.
- **Failure bắt buộc quan sát:** carrier timeout hoặc bảng giá không tồn tại; ở mức pattern cần chú ý thêm duplicate delivery, ordering hoặc subscriber thất bại.

```mermaid
flowchart TD
    A[ShippingQuote] --> E[Domain event]
    E --> D[Dispatcher]
    D --> O1[Audit subscriber]
    D --> O2[Notification subscriber]
    D --> O3[Projection subscriber]
    O2 -. idempotency .-> F[Stale rate]
    O3 --> R[Shipping option]
```

## Cách chạy

```bash
php playground/029-observer-shipping/index.php
```

Trước khi chạy, dự đoán output và ghi lại concrete detail mà client đang biết. Sau khi chạy, đối chiếu xem **publisher không biết concrete subscriber** đã làm client biết ít đi điều gì, đồng thời kiểm tra invariant của shipping vẫn được giữ.

## Thử nghiệm có hướng dẫn

1. Chạy baseline và lưu output/error hiện tại.
2. Thêm carrier mới và kiểm tra fallback không làm sai sla.
3. Tạo failure **carrier timeout hoặc bảng giá không tồn tại** và yêu cầu lỗi được biểu diễn rõ, không silent fallback.
4. Viết test tập trung vào **phí và SLA phải nhất quán với tuyến giao hàng**.
5. Thử bỏ abstraction; nếu code trực tiếp vẫn rõ và change axis chưa tồn tại, ghi lại lý do chưa áp dụng Observer.

## Câu hỏi review

- Trong miền shipping, phần nào là policy/domain rule và phần nào chỉ là wiring?
- Observer bảo vệ thay đổi **thêm subscriber mà không sửa publisher** bằng cơ chế nào?
- Failure **carrier timeout hoặc bảng giá không tồn tại** được phát hiện, dịch và quan sát ở boundary nào?
- Abstraction có làm invariant **phí và SLA phải nhất quán với tuyến giao hàng** dễ test hơn không?
- Complexity mới xuất hiện ở đâu: số class, ordering, lifecycle hay debugging?

## Kết quả mong đợi

Một lời giải đạt yêu cầu phải chứng minh flow shipping vẫn giữ invariant khi thay implementation observer, có assertion cho failure đã nêu và giải thích được trade-off của boundary mới. Không chấm điểm dựa trên số lượng interface hoặc class.
