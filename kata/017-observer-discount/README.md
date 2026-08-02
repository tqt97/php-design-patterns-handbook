# Kata 17: Observer trong Discount

## Bối cảnh và lý do chọn bài

Module **Discount** đang đánh giá coupon và promotion. Invariant bắt buộc là **tổng giảm không vượt giá trị đơn**; failure cần quan sát là **nhiều rule cùng áp dụng hoặc stacking sai**. Kata này dùng **Observer** để luyện cách tách các reaction sau một sự kiện khỏi publisher để thêm subscriber độc lập. Đây là tình huống tổng hợp phục vụ học tập, không giả định có một đề bài ẩn khác.

## Code smell cần tìm

Hãy dựng hoặc đọc starter code và đánh dấu nơi `'DiscountService'` vừa điều phối use case, vừa biết concrete detail thuộc change axis của **Observer**. Dấu hiệu cần refactor không phải method dài đơn thuần, mà là mỗi lần thay đổi Discount đều buộc sửa cùng nhánh, cùng dependency hoặc cùng lifecycle logic.

## Mục tiêu thiết kế

Publisher phát event contract; subscriber xử lý side effect theo delivery semantics đã chọn. Sau refactor, client phải biết ít concrete detail hơn và invariant **tổng giảm không vượt giá trị đơn** vẫn được bảo vệ.

## Acceptance criteria

- Characterization test khóa behavior hiện tại trước khi thay cấu trúc.
- Test từ chối trường hợp vi phạm **tổng giảm không vượt giá trị đơn**.
- Failure **nhiều rule cùng áp dụng hoặc stacking sai** có exception/result rõ với caller, không silent fallback.
- Có scenario thứ hai chứng minh extension point của **Observer** mà không sửa orchestration ổn định.
- Test trọng tâm: test payload, subscriber count, duplicate delivery, ordering và lỗi một subscriber.
- README lời giải nêu trade-off và điều kiện không nên dùng: dùng event để che call flow đơn giản hoặc không định nghĩa transaction/delivery semantics.

## Hướng dẫn từng bước

1. Chạy `solution.php` hoặc starter hiện có; ghi output, exception và side effect.
2. Viết test cho happy path của **Discount**, invariant **tổng giảm không vượt giá trị đơn** và failure **nhiều rule cùng áp dụng hoặc stacking sai**.
3. Vẽ dependency/collaboration trước refactor; khoanh đúng change axis mà **Observer** sẽ bảo vệ.
4. Tách một responsibility mỗi lần, chạy test sau từng thay đổi; chưa đổi public API nếu chưa cần.
5. Thêm biến thể thứ hai hoặc fault injection đặc trưng cho Discount.
6. Vẽ sơ đồ sau refactor và so sánh concrete detail nào biến mất khỏi client.
7. Ghi một đoạn ngắn giải thích vì sao thiết kế trực tiếp có thể tốt hơn nếu dùng event để che call flow đơn giản hoặc không định nghĩa transaction/delivery semantics.

## Sơ đồ mục tiêu

```mermaid
sequenceDiagram
    participant S as DiscountResultService
    participant E as EventDispatcher
    participant A as AuditSubscriber
    participant P as ProjectionSubscriber
    S->>E: publish DiscountResultChanged
    E->>A: append audit evidence
    E->>P: update read model
    Note over E,P: subscriber failure phải độc lập và quan sát được
```

Sơ đồ mô tả đúng cơ chế **Observer** trong miền **Discount**. Khi triển khai, hãy giữ invariant: **không áp dụng ưu đãi ngoài điều kiện**. Participant trong sơ đồ là vocabulary gợi ý; đổi tên được, nhưng hướng phụ thuộc và failure boundary không được đảo ngược.

## Câu hỏi review

1. Change axis của **Observer** trong Discount có bằng chứng từ requirement hay chỉ là dự đoán?
2. Test nào sẽ thất bại nếu concrete detail quay lại `'DiscountService'`?
3. Failure **nhiều rule cùng áp dụng hoặc stacking sai** được translate ở boundary nào?
4. Metric/log nào phát hiện vi phạm **tổng giảm không vượt giá trị đơn** trong production?
5. Chi phí type, wiring và call flow có nhỏ hơn rủi ro **dùng event để che call flow đơn giản hoặc không định nghĩa transaction/delivery semantics** không?

## Gợi ý lời giải

Bắt đầu từ behavior contract thay vì tên participant trong sách. Với **Observer**, hãy chứng minh `test payload, subscriber count, duplicate delivery, ordering và lỗi một subscriber` trước khi tối ưu cấu trúc. Lời giải tốt nhất là lời giải nhỏ nhất làm rõ ownership, invariant và failure semantics của Discount.

## Chạy

```bash
php kata/017-observer-discount/solution.php
```

## Tài liệu liên quan

- Bài liên quan trực tiếp: **Observer** trong **Discount**; dùng liên kết dưới đây để đối chiếu lý thuyết và bài thực hành.
- [Design Pattern overview](../../OVERVIEW.md)
- [Core pattern articles](../../docs/README.md)
- [Exercises có lời giải](../../exercises/README.md)
- [Playground](../../playground/README.md)
