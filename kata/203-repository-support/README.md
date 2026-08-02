# Kata 203: Repository trong Support

## Bối cảnh và lý do chọn bài

Module **Support** đang phân loại ticket và định tuyến đội xử lý. Invariant bắt buộc là **ticket khẩn cấp không bị hạ ưu tiên**; failure cần quan sát là **SLA hoặc routing rule xung đột**. Kata này dùng **Repository** để luyện cách truy cập aggregate như collection domain và che mapping/persistence detail. Đây là tình huống tổng hợp phục vụ học tập, không giả định có một đề bài ẩn khác.

## Code smell cần tìm

Hãy dựng hoặc đọc starter code và đánh dấu nơi `'SupportService'` vừa điều phối use case, vừa biết concrete detail thuộc change axis của **Repository**. Dấu hiệu cần refactor không phải method dài đơn thuần, mà là mỗi lần thay đổi Support đều buộc sửa cùng nhánh, cùng dependency hoặc cùng lifecycle logic.

## Mục tiêu thiết kế

Application phụ thuộc repository port; adapter ORM/SQL implement contract theo use case. Sau refactor, client phải biết ít concrete detail hơn và invariant **ticket khẩn cấp không bị hạ ưu tiên** vẫn được bảo vệ.

## Acceptance criteria

- Characterization test khóa behavior hiện tại trước khi thay cấu trúc.
- Test từ chối trường hợp vi phạm **ticket khẩn cấp không bị hạ ưu tiên**.
- Failure **SLA hoặc routing rule xung đột** có exception/result rõ với caller, không silent fallback.
- Có scenario thứ hai chứng minh extension point của **Repository** mà không sửa orchestration ổn định.
- Test trọng tâm: add/find/not-found/uniqueness và parity giữa in-memory với adapter thật.
- README lời giải nêu trade-off và điều kiện không nên dùng: generic CRUD wrapper hoặc trả query builder.

## Hướng dẫn từng bước

1. Chạy `solution.php` hoặc starter hiện có; ghi output, exception và side effect.
2. Viết test cho happy path của **Support**, invariant **ticket khẩn cấp không bị hạ ưu tiên** và failure **SLA hoặc routing rule xung đột**.
3. Vẽ dependency/collaboration trước refactor; khoanh đúng change axis mà **Repository** sẽ bảo vệ.
4. Tách một responsibility mỗi lần, chạy test sau từng thay đổi; chưa đổi public API nếu chưa cần.
5. Thêm biến thể thứ hai hoặc fault injection đặc trưng cho Support.
6. Vẽ sơ đồ sau refactor và so sánh concrete detail nào biến mất khỏi client.
7. Ghi một đoạn ngắn giải thích vì sao thiết kế trực tiếp có thể tốt hơn nếu generic CRUD wrapper hoặc trả query builder.

## Sơ đồ mục tiêu

```mermaid
classDiagram
    class SupportDecisionRepository {
      <<interface>>
      +get(id) SupportDecision
      +save(SupportDecision, version)
    }
    class InMemorySupportDecisionRepository
    class SqlSupportDecisionRepository
    class SupportDecision
    SupportDecisionRepository <|.. InMemorySupportDecisionRepository
    SupportDecisionRepository <|.. SqlSupportDecisionRepository
    SupportDecisionRepository --> SupportDecision
```

Sơ đồ mô tả đúng cơ chế **Repository** trong miền **Support**. Khi triển khai, hãy giữ invariant: **SLA và escalation không bị bỏ qua**. Participant trong sơ đồ là vocabulary gợi ý; đổi tên được, nhưng hướng phụ thuộc và failure boundary không được đảo ngược.

## Câu hỏi review

1. Change axis của **Repository** trong Support có bằng chứng từ requirement hay chỉ là dự đoán?
2. Test nào sẽ thất bại nếu concrete detail quay lại `'SupportService'`?
3. Failure **SLA hoặc routing rule xung đột** được translate ở boundary nào?
4. Metric/log nào phát hiện vi phạm **ticket khẩn cấp không bị hạ ưu tiên** trong production?
5. Chi phí type, wiring và call flow có nhỏ hơn rủi ro **generic CRUD wrapper hoặc trả query builder** không?

## Gợi ý lời giải

Bắt đầu từ behavior contract thay vì tên participant trong sách. Với **Repository**, hãy chứng minh `add/find/not-found/uniqueness và parity giữa in-memory với adapter thật` trước khi tối ưu cấu trúc. Lời giải tốt nhất là lời giải nhỏ nhất làm rõ ownership, invariant và failure semantics của Support.

## Chạy

```bash
php kata/203-repository-support/solution.php
```

## Tài liệu liên quan

- Bài liên quan trực tiếp: **Repository** trong **Support**; dùng liên kết dưới đây để đối chiếu lý thuyết và bài thực hành.
- [Design Pattern overview](../../OVERVIEW.md)
- [Core pattern articles](../../docs/README.md)
- [Exercises có lời giải](../../exercises/README.md)
- [Playground](../../playground/README.md)
