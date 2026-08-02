# Kata 44: Chain of Responsibility trong Shipping

## Bối cảnh và lý do chọn bài

Module **Shipping** đang tính phí và ETA theo vùng. Invariant bắt buộc là **không chọn service không hỗ trợ địa chỉ**; failure cần quan sát là **carrier timeout hoặc rounding sai**. Kata này dùng **Chain of Responsibility** để luyện cách tách các rule/handler có thể xử lý hoặc chuyển request cho bước kế tiếp. Đây là tình huống tổng hợp phục vụ học tập, không giả định có một đề bài ẩn khác.

## Code smell cần tìm

Hãy dựng hoặc đọc starter code và đánh dấu nơi `'ShippingService'` vừa điều phối use case, vừa biết concrete detail thuộc change axis của **Chain of Responsibility**. Dấu hiệu cần refactor không phải method dài đơn thuần, mà là mỗi lần thay đổi Shipping đều buộc sửa cùng nhánh, cùng dependency hoặc cùng lifecycle logic.

## Mục tiêu thiết kế

Ordered handlers cùng contract; mỗi handler sở hữu điều kiện nhận và short-circuit. Sau refactor, client phải biết ít concrete detail hơn và invariant **không chọn service không hỗ trợ địa chỉ** vẫn được bảo vệ.

## Acceptance criteria

- Characterization test khóa behavior hiện tại trước khi thay cấu trúc.
- Test từ chối trường hợp vi phạm **không chọn service không hỗ trợ địa chỉ**.
- Failure **carrier timeout hoặc rounding sai** có exception/result rõ với caller, không silent fallback.
- Có scenario thứ hai chứng minh extension point của **Chain of Responsibility** mà không sửa orchestration ổn định.
- Test trọng tâm: ordering, short-circuit, unhandled request và mutation của payload.
- README lời giải nêu trade-off và điều kiện không nên dùng: ordering trở thành business rule ẩn và khó trace.

## Hướng dẫn từng bước

1. Chạy `solution.php` hoặc starter hiện có; ghi output, exception và side effect.
2. Viết test cho happy path của **Shipping**, invariant **không chọn service không hỗ trợ địa chỉ** và failure **carrier timeout hoặc rounding sai**.
3. Vẽ dependency/collaboration trước refactor; khoanh đúng change axis mà **Chain of Responsibility** sẽ bảo vệ.
4. Tách một responsibility mỗi lần, chạy test sau từng thay đổi; chưa đổi public API nếu chưa cần.
5. Thêm biến thể thứ hai hoặc fault injection đặc trưng cho Shipping.
6. Vẽ sơ đồ sau refactor và so sánh concrete detail nào biến mất khỏi client.
7. Ghi một đoạn ngắn giải thích vì sao thiết kế trực tiếp có thể tốt hơn nếu ordering trở thành business rule ẩn và khó trace.

## Sơ đồ mục tiêu

```mermaid
flowchart LR
    I[ShipmentRequest] --> H1[Validate handler]
    H1 --> H2[Policy handler]
    H2 --> H3[Fallback / escalation handler]
    H1 -. reject .-> X[Decision]
    H2 -. handled .-> X
    H3 --> X
```

Sơ đồ mô tả đúng cơ chế **Chain** trong miền **Shipping**. Khi triển khai, hãy giữ invariant: **phí và SLA phản ánh đúng tuyến giao**. Participant trong sơ đồ là vocabulary gợi ý; đổi tên được, nhưng hướng phụ thuộc và failure boundary không được đảo ngược.

## Câu hỏi review

1. Change axis của **Chain of Responsibility** trong Shipping có bằng chứng từ requirement hay chỉ là dự đoán?
2. Test nào sẽ thất bại nếu concrete detail quay lại `'ShippingService'`?
3. Failure **carrier timeout hoặc rounding sai** được translate ở boundary nào?
4. Metric/log nào phát hiện vi phạm **không chọn service không hỗ trợ địa chỉ** trong production?
5. Chi phí type, wiring và call flow có nhỏ hơn rủi ro **ordering trở thành business rule ẩn và khó trace** không?

## Gợi ý lời giải

Bắt đầu từ behavior contract thay vì tên participant trong sách. Với **Chain of Responsibility**, hãy chứng minh `ordering, short-circuit, unhandled request và mutation của payload` trước khi tối ưu cấu trúc. Lời giải tốt nhất là lời giải nhỏ nhất làm rõ ownership, invariant và failure semantics của Shipping.

## Chạy

```bash
php kata/044-chain-shipping/solution.php
```

## Tài liệu liên quan

- Bài liên quan trực tiếp: **Chain of Responsibility** trong **Shipping**; dùng liên kết dưới đây để đối chiếu lý thuyết và bài thực hành.
- [Design Pattern overview](../../OVERVIEW.md)
- [Core pattern articles](../../docs/README.md)
- [Exercises có lời giải](../../exercises/README.md)
- [Playground](../../playground/README.md)
