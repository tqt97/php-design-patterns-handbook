# Kata 19: Command trong Crm

## Bối cảnh và lý do chọn bài

Module **Crm** đang xử lý nghiệp vụ Crm. Invariant bắt buộc là **invariant của Crm**; failure cần quan sát là **failure path của Crm**. Kata này dùng **Command** để luyện cách đóng gói business intent thành object để audit, queue, retry hoặc phân quyền rõ. Đây là tình huống tổng hợp phục vụ học tập, không giả định có một đề bài ẩn khác.

## Code smell cần tìm

Hãy dựng hoặc đọc starter code và đánh dấu nơi `'CrmService'` vừa điều phối use case, vừa biết concrete detail thuộc change axis của **Command**. Dấu hiệu cần refactor không phải method dài đơn thuần, mà là mỗi lần thay đổi Crm đều buộc sửa cùng nhánh, cùng dependency hoặc cùng lifecycle logic.

## Mục tiêu thiết kế

Command chứa dữ liệu intent; handler điều phối use case và dependency. Sau refactor, client phải biết ít concrete detail hơn và invariant **invariant của Crm** vẫn được bảo vệ.

## Acceptance criteria

- Characterization test khóa behavior hiện tại trước khi thay cấu trúc.
- Test từ chối trường hợp vi phạm **invariant của Crm**.
- Failure **failure path của Crm** có exception/result rõ với caller, không silent fallback.
- Có scenario thứ hai chứng minh extension point của **Command** mà không sửa orchestration ổn định.
- Test trọng tâm: validation, authorization, idempotency key, retry và audit metadata.
- README lời giải nêu trade-off và điều kiện không nên dùng: command chỉ là DTO đổi tên hoặc mỗi method nhỏ đều tạo handler không cần thiết.

## Hướng dẫn từng bước

1. Chạy `solution.php` hoặc starter hiện có; ghi output, exception và side effect.
2. Viết test cho happy path của **Crm**, invariant **invariant của Crm** và failure **failure path của Crm**.
3. Vẽ dependency/collaboration trước refactor; khoanh đúng change axis mà **Command** sẽ bảo vệ.
4. Tách một responsibility mỗi lần, chạy test sau từng thay đổi; chưa đổi public API nếu chưa cần.
5. Thêm biến thể thứ hai hoặc fault injection đặc trưng cho Crm.
6. Vẽ sơ đồ sau refactor và so sánh concrete detail nào biến mất khỏi client.
7. Ghi một đoạn ngắn giải thích vì sao thiết kế trực tiếp có thể tốt hơn nếu command chỉ là DTO đổi tên hoặc mỗi method nhỏ đều tạo handler không cần thiết.

## Sơ đồ mục tiêu

```mermaid
sequenceDiagram
    participant C as Controller
    participant B as CommandBus
    participant H as CustomerRequestHandler
    participant R as Repository
    C->>B: dispatch(CustomerRequest)
    B->>H: handle
    H->>R: load/save CustomerProfile
    R-->>H: version/result
    H-->>C: command outcome
```

Sơ đồ mô tả đúng cơ chế **Command** trong miền **Crm**. Khi triển khai, hãy giữ invariant: **consent và provenance không bị mất**. Participant trong sơ đồ là vocabulary gợi ý; đổi tên được, nhưng hướng phụ thuộc và failure boundary không được đảo ngược.

## Câu hỏi review

1. Change axis của **Command** trong Crm có bằng chứng từ requirement hay chỉ là dự đoán?
2. Test nào sẽ thất bại nếu concrete detail quay lại `'CrmService'`?
3. Failure **failure path của Crm** được translate ở boundary nào?
4. Metric/log nào phát hiện vi phạm **invariant của Crm** trong production?
5. Chi phí type, wiring và call flow có nhỏ hơn rủi ro **command chỉ là DTO đổi tên hoặc mỗi method nhỏ đều tạo handler không cần thiết** không?

## Gợi ý lời giải

Bắt đầu từ behavior contract thay vì tên participant trong sách. Với **Command**, hãy chứng minh `validation, authorization, idempotency key, retry và audit metadata` trước khi tối ưu cấu trúc. Lời giải tốt nhất là lời giải nhỏ nhất làm rõ ownership, invariant và failure semantics của Crm.

## Chạy

```bash
php kata/019-command-crm/solution.php
```

## Tài liệu liên quan

- Bài liên quan trực tiếp: **Command** trong **Crm**; dùng liên kết dưới đây để đối chiếu lý thuyết và bài thực hành.
- [Design Pattern overview](../../OVERVIEW.md)
- [Core pattern articles](../../docs/README.md)
- [Exercises có lời giải](../../exercises/README.md)
- [Playground](../../playground/README.md)
