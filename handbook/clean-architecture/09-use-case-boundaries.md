# Use Case Boundaries

## Vấn đề cần giải quyết

**Use Case Boundaries** giải quyết một quyết định cụ thể trong clean architecture: làm sao giữ rule và ownership rõ khi hệ thống thay đổi, thay vì để framework, dữ liệu hoặc quy trình vận hành quyết định ngược lại thiết kế.

Để đánh giá **Use Case Boundaries**, hãy tìm triệu chứng cụ thể trong nhóm clean architecture: dependency lan rộng, owner mơ hồ, test phải dựng sai boundary hoặc rollback không an toàn. Không có bằng chứng như vậy thì chưa nên thêm cấu trúc mới.

## Khái niệm trọng tâm

### 1. Change Boundary

**Change Boundary** là khái niệm cần dùng để phân tích Use Case Boundaries. Hãy xác định nó nằm ở class, dữ liệu hay quy trình nào; ai sở hữu quyết định; invariant nào phụ thuộc vào nó; và failure nào xảy ra khi boundary bị đặt sai. Một định nghĩa chỉ có giá trị khi liên hệ được với code hoặc quyết định vận hành cụ thể.

### 2. Data Crossing Boundary

**Data Crossing Boundary** là khái niệm cần dùng để phân tích Use Case Boundaries. Hãy xác định nó nằm ở class, dữ liệu hay quy trình nào; ai sở hữu quyết định; invariant nào phụ thuộc vào nó; và failure nào xảy ra khi boundary bị đặt sai. Một định nghĩa chỉ có giá trị khi liên hệ được với code hoặc quyết định vận hành cụ thể.

### 3. Dependency Direction

**Dependency Direction** là khái niệm cần dùng để phân tích Use Case Boundaries. Hãy xác định nó nằm ở class, dữ liệu hay quy trình nào; ai sở hữu quyết định; invariant nào phụ thuộc vào nó; và failure nào xảy ra khi boundary bị đặt sai. Một định nghĩa chỉ có giá trị khi liên hệ được với code hoặc quyết định vận hành cụ thể.

### 4. Contract Ownership

**Contract Ownership** là khái niệm cần dùng để phân tích Use Case Boundaries. Hãy xác định nó nằm ở class, dữ liệu hay quy trình nào; ai sở hữu quyết định; invariant nào phụ thuộc vào nó; và failure nào xảy ra khi boundary bị đặt sai. Một định nghĩa chỉ có giá trị khi liên hệ được với code hoặc quyết định vận hành cụ thể.

## Mental model

### Use-case input/output boundary

Input/output của use case dùng kiểu ổn định theo nghiệp vụ, không truyền Request, ORM model hoặc framework response vào core.

```mermaid
flowchart LR
    RAW[Raw request] --> MAP[Delivery mapper]
    MAP --> CMD[Typed input]
    CMD --> UC[Use case]
    UC --> OUT[Result model]
    OUT --> VIEW[Presenter]
    VIEW --> RAWOUT[HTTP/CLI response]
```

**Cách đọc sơ đồ Use Case Boundaries:** xác định điểm khởi đầu, quyết định trung tâm và outcome trong sơ đồ; sau đó ánh xạ từng participant sang artifact thật của nhóm clean architecture. Khi review, kiểm tra failure path và bằng chứng đặc thù của Use Case Boundaries, thay vì chỉ đánh giá hình thức các mũi tên.
## Ví dụ áp dụng

Để áp dụng **use case boundaries**, hãy chọn một use case có liên quan trực tiếp rồi ghi rõ:

1. trạng thái hoặc quyết định nào của **use case boundaries** cần nhất quán;
2. dependency hoặc boundary nào có thể thất bại;
3. ai sở hữu rule, dữ liệu và vận hành của chủ đề này;
4. test, artifact hoặc metric nào chứng minh cách áp dụng use case boundaries là đúng.

Chỉ áp dụng **use case boundaries** khi nó làm các điểm trên rõ và kiểm chứng được hơn so với thiết kế trực tiếp.

## Quy trình áp dụng

1. Viết một scenario cụ thể và outcome mong đợi, chưa dùng thuật ngữ Use Case Boundaries.
2. Ghi invariant, owner và failure mode liên quan đến **change boundary**.
3. Vẽ dependency/data flow hiện tại; đánh dấu nơi detail đang rò vào policy.
4. So sánh baseline trực tiếp với một phương án có boundary rõ hơn.
5. Chọn thay đổi nhỏ nhất có thể chứng minh bằng test hoặc metric.
6. Ghi migration, rollback và điều kiện xóa abstraction nếu giả định không còn đúng.

## Sai lầm thường gặp

- Gọi một cấu trúc là use case boundaries nhưng không thay đổi semantics hoặc ownership.
- Áp dụng theo framework recipe mà chưa xác định vấn đề riêng của use case boundaries.
- Không ghi trade-off đặc thù: coupling, consistency, latency, migration hoặc cognitive load.
- Không kiểm tra failure/boundary có liên quan trực tiếp đến use case boundaries.

## Câu hỏi review

- Use Case Boundaries đang bảo vệ invariant nào và owner nào chịu trách nhiệm?
- Contract có phản ánh **change boundary** hay chỉ là tên kỹ thuật chung chung?
- Failure ở boundary được classify, retry hoặc translate ở đâu?
- Test/metric nào chứng minh thiết kế tốt hơn baseline?
- Khi nào có thể hợp nhất hoặc xóa cấu trúc này mà không mất semantics?

## Bài tập

Chọn một module thật có liên quan đến **Use Case Boundaries**. Viết design note gồm: scenario hiện tại, dependency/data-flow diagram, invariant, hai alternative, decision, test/metric và rollback. Sau đó thực hiện một refactor nhỏ hoặc spike để chứng minh assumption quan trọng nhất; không kết luận chỉ bằng sơ đồ.

## Góc nhìn nâng cao của chương này

Chương `09-use-case-boundaries.md` tập trung vào áp dụng và vận hành **use case boundaries** ở quy mô production; không lặp lại phần nhập môn ở các chương đầu cùng nhóm.
