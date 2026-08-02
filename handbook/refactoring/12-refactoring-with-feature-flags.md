# Refactoring With Feature Flags

## Vấn đề cần giải quyết

**Refactoring With Feature Flags** giải quyết một quyết định cụ thể trong refactoring: làm sao giữ rule và ownership rõ khi hệ thống thay đổi, thay vì để framework, dữ liệu hoặc quy trình vận hành quyết định ngược lại thiết kế.

Để đánh giá **Refactoring With Feature Flags**, hãy tìm triệu chứng cụ thể trong nhóm refactoring: dependency lan rộng, owner mơ hồ, test phải dựng sai boundary hoặc rollback không an toàn. Không có bằng chứng như vậy thì chưa nên thêm cấu trúc mới.

## Khái niệm trọng tâm

### 1. Release Vs Deploy

**Release Vs Deploy** là khái niệm cần dùng để phân tích Refactoring With Feature Flags. Hãy xác định nó nằm ở class, dữ liệu hay quy trình nào; ai sở hữu quyết định; invariant nào phụ thuộc vào nó; và failure nào xảy ra khi boundary bị đặt sai. Một định nghĩa chỉ có giá trị khi liên hệ được với code hoặc quyết định vận hành cụ thể.

### 2. Targeting

**Targeting** là khái niệm cần dùng để phân tích Refactoring With Feature Flags. Hãy xác định nó nằm ở class, dữ liệu hay quy trình nào; ai sở hữu quyết định; invariant nào phụ thuộc vào nó; và failure nào xảy ra khi boundary bị đặt sai. Một định nghĩa chỉ có giá trị khi liên hệ được với code hoặc quyết định vận hành cụ thể.

### 3. Kill Switch

**Kill Switch** là khái niệm cần dùng để phân tích Refactoring With Feature Flags. Hãy xác định nó nằm ở class, dữ liệu hay quy trình nào; ai sở hữu quyết định; invariant nào phụ thuộc vào nó; và failure nào xảy ra khi boundary bị đặt sai. Một định nghĩa chỉ có giá trị khi liên hệ được với code hoặc quyết định vận hành cụ thể.

### 4. Flag Cleanup

**Flag Cleanup** là khái niệm cần dùng để phân tích Refactoring With Feature Flags. Hãy xác định nó nằm ở class, dữ liệu hay quy trình nào; ai sở hữu quyết định; invariant nào phụ thuộc vào nó; và failure nào xảy ra khi boundary bị đặt sai. Một định nghĩa chỉ có giá trị khi liên hệ được với code hoặc quyết định vận hành cụ thể.

## Mental model

### Feature-flag refactoring

Feature flag tạo migration seam nhưng cũng tạo nhiều trạng thái. Mỗi flag cần owner, expiry, telemetry và cleanup plan.

```mermaid
flowchart LR
    C[Caller] --> F{Flag/cohort}
    F --> OLD[Old path]
    F --> NEW[New path]
    OLD --> CMP[Outcome comparison]
    NEW --> CMP
    CMP --> R[Rollout or rollback]
    R --> CLEAN[Remove flag and old code]
```

**Cách đọc sơ đồ Refactoring With Feature Flags:** xác định điểm khởi đầu, quyết định trung tâm và outcome trong sơ đồ; sau đó ánh xạ từng participant sang artifact thật của nhóm refactoring. Khi review, kiểm tra failure path và bằng chứng đặc thù của Refactoring With Feature Flags, thay vì chỉ đánh giá hình thức các mũi tên.
## Ví dụ áp dụng

Để áp dụng **refactoring with feature flags**, hãy chọn một use case có liên quan trực tiếp rồi ghi rõ:

1. trạng thái hoặc quyết định nào của **refactoring with feature flags** cần nhất quán;
2. dependency hoặc boundary nào có thể thất bại;
3. ai sở hữu rule, dữ liệu và vận hành của chủ đề này;
4. test, artifact hoặc metric nào chứng minh cách áp dụng refactoring with feature flags là đúng.

Chỉ áp dụng **refactoring with feature flags** khi nó làm các điểm trên rõ và kiểm chứng được hơn so với thiết kế trực tiếp.

## Quy trình áp dụng

1. Viết một scenario cụ thể và outcome mong đợi, chưa dùng thuật ngữ Refactoring With Feature Flags.
2. Ghi invariant, owner và failure mode liên quan đến **release vs deploy**.
3. Vẽ dependency/data flow hiện tại; đánh dấu nơi detail đang rò vào policy.
4. So sánh baseline trực tiếp với một phương án có boundary rõ hơn.
5. Chọn thay đổi nhỏ nhất có thể chứng minh bằng test hoặc metric.
6. Ghi migration, rollback và điều kiện xóa abstraction nếu giả định không còn đúng.

## Sai lầm thường gặp

- Gọi một cấu trúc là refactoring with feature flags nhưng không thay đổi semantics hoặc ownership.
- Áp dụng theo framework recipe mà chưa xác định vấn đề riêng của refactoring with feature flags.
- Không ghi trade-off đặc thù: coupling, consistency, latency, migration hoặc cognitive load.
- Không kiểm tra failure/boundary có liên quan trực tiếp đến refactoring with feature flags.

## Câu hỏi review

- Refactoring With Feature Flags đang bảo vệ invariant nào và owner nào chịu trách nhiệm?
- Contract có phản ánh **release vs deploy** hay chỉ là tên kỹ thuật chung chung?
- Failure ở boundary được classify, retry hoặc translate ở đâu?
- Test/metric nào chứng minh thiết kế tốt hơn baseline?
- Khi nào có thể hợp nhất hoặc xóa cấu trúc này mà không mất semantics?

## Bài tập

Chọn một module thật có liên quan đến **Refactoring With Feature Flags**. Viết design note gồm: scenario hiện tại, dependency/data-flow diagram, invariant, hai alternative, decision, test/metric và rollback. Sau đó thực hiện một refactor nhỏ hoặc spike để chứng minh assumption quan trọng nhất; không kết luận chỉ bằng sơ đồ.

## Góc nhìn nâng cao của chương này

Chương `12-refactoring-with-feature-flags.md` tập trung vào áp dụng và vận hành **refactoring with feature flags** ở quy mô production; không lặp lại phần nhập môn ở các chương đầu cùng nhóm.
