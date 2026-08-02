# Refactoring Database Boundaries

## Vấn đề cần giải quyết

**Refactoring Database Boundaries** giải quyết một quyết định cụ thể trong refactoring: làm sao giữ rule và ownership rõ khi hệ thống thay đổi, thay vì để framework, dữ liệu hoặc quy trình vận hành quyết định ngược lại thiết kế.

Để đánh giá **Refactoring Database Boundaries**, hãy tìm triệu chứng cụ thể trong nhóm refactoring: dependency lan rộng, owner mơ hồ, test phải dựng sai boundary hoặc rollback không an toàn. Không có bằng chứng như vậy thì chưa nên thêm cấu trúc mới.

## Khái niệm trọng tâm

### 1. Change Boundary

**Change Boundary** là khái niệm cần dùng để phân tích Refactoring Database Boundaries. Hãy xác định nó nằm ở class, dữ liệu hay quy trình nào; ai sở hữu quyết định; invariant nào phụ thuộc vào nó; và failure nào xảy ra khi boundary bị đặt sai. Một định nghĩa chỉ có giá trị khi liên hệ được với code hoặc quyết định vận hành cụ thể.

### 2. Data Crossing Boundary

**Data Crossing Boundary** là khái niệm cần dùng để phân tích Refactoring Database Boundaries. Hãy xác định nó nằm ở class, dữ liệu hay quy trình nào; ai sở hữu quyết định; invariant nào phụ thuộc vào nó; và failure nào xảy ra khi boundary bị đặt sai. Một định nghĩa chỉ có giá trị khi liên hệ được với code hoặc quyết định vận hành cụ thể.

### 3. Dependency Direction

**Dependency Direction** là khái niệm cần dùng để phân tích Refactoring Database Boundaries. Hãy xác định nó nằm ở class, dữ liệu hay quy trình nào; ai sở hữu quyết định; invariant nào phụ thuộc vào nó; và failure nào xảy ra khi boundary bị đặt sai. Một định nghĩa chỉ có giá trị khi liên hệ được với code hoặc quyết định vận hành cụ thể.

### 4. Contract Ownership

**Contract Ownership** là khái niệm cần dùng để phân tích Refactoring Database Boundaries. Hãy xác định nó nằm ở class, dữ liệu hay quy trình nào; ai sở hữu quyết định; invariant nào phụ thuộc vào nó; và failure nào xảy ra khi boundary bị đặt sai. Một định nghĩa chỉ có giá trị khi liên hệ được với code hoặc quyết định vận hành cụ thể.

## Mental model

### Database boundary migration

Khi đổi database boundary, hãy kiểm soát dual write, backfill, read switch và reconciliation. Dữ liệu sai âm thầm nguy hiểm hơn lỗi deploy rõ ràng.

```mermaid
sequenceDiagram
    participant App as Application
    participant Old as Old schema
    participant New as New schema
    App->>Old: write legacy representation
    App->>New: write new representation
    App->>Old: read and shadow-compare New
    App->>New: switch authoritative read
    App->>Old: stop write after verification
```

**Cách đọc sơ đồ Refactoring Database Boundaries:** xác định điểm khởi đầu, quyết định trung tâm và outcome trong sơ đồ; sau đó ánh xạ từng participant sang artifact thật của nhóm refactoring. Khi review, kiểm tra failure path và bằng chứng đặc thù của Refactoring Database Boundaries, thay vì chỉ đánh giá hình thức các mũi tên.
## Ví dụ áp dụng

Để áp dụng **refactoring database boundaries**, hãy chọn một use case có liên quan trực tiếp rồi ghi rõ:

1. trạng thái hoặc quyết định nào của **refactoring database boundaries** cần nhất quán;
2. dependency hoặc boundary nào có thể thất bại;
3. ai sở hữu rule, dữ liệu và vận hành của chủ đề này;
4. test, artifact hoặc metric nào chứng minh cách áp dụng refactoring database boundaries là đúng.

Chỉ áp dụng **refactoring database boundaries** khi nó làm các điểm trên rõ và kiểm chứng được hơn so với thiết kế trực tiếp.

## Quy trình áp dụng

1. Viết một scenario cụ thể và outcome mong đợi, chưa dùng thuật ngữ Refactoring Database Boundaries.
2. Ghi invariant, owner và failure mode liên quan đến **change boundary**.
3. Vẽ dependency/data flow hiện tại; đánh dấu nơi detail đang rò vào policy.
4. So sánh baseline trực tiếp với một phương án có boundary rõ hơn.
5. Chọn thay đổi nhỏ nhất có thể chứng minh bằng test hoặc metric.
6. Ghi migration, rollback và điều kiện xóa abstraction nếu giả định không còn đúng.

## Sai lầm thường gặp

- Gọi một cấu trúc là refactoring database boundaries nhưng không thay đổi semantics hoặc ownership.
- Áp dụng theo framework recipe mà chưa xác định vấn đề riêng của refactoring database boundaries.
- Không ghi trade-off đặc thù: coupling, consistency, latency, migration hoặc cognitive load.
- Không kiểm tra failure/boundary có liên quan trực tiếp đến refactoring database boundaries.

## Câu hỏi review

- Refactoring Database Boundaries đang bảo vệ invariant nào và owner nào chịu trách nhiệm?
- Contract có phản ánh **change boundary** hay chỉ là tên kỹ thuật chung chung?
- Failure ở boundary được classify, retry hoặc translate ở đâu?
- Test/metric nào chứng minh thiết kế tốt hơn baseline?
- Khi nào có thể hợp nhất hoặc xóa cấu trúc này mà không mất semantics?

## Bài tập

Chọn một module thật có liên quan đến **Refactoring Database Boundaries**. Viết design note gồm: scenario hiện tại, dependency/data-flow diagram, invariant, hai alternative, decision, test/metric và rollback. Sau đó thực hiện một refactor nhỏ hoặc spike để chứng minh assumption quan trọng nhất; không kết luận chỉ bằng sơ đồ.

## Góc nhìn nâng cao của chương này

Chương `11-refactoring-database-boundaries.md` tập trung vào áp dụng và vận hành **refactoring database boundaries** ở quy mô production; không lặp lại phần nhập môn ở các chương đầu cùng nhóm.
