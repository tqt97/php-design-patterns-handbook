# Quiz — 02 Ddd Boundaries

## 1. Bounded Context là gì?

**Đáp án gợi ý:** Ranh giới model/language/ownership nhất quán, không chỉ module folder.

## 2. Aggregate boundary dựa trên gì?

**Đáp án gợi ý:** Invariant cần strong consistency trong một transaction.

## 3. Context Map mô tả gì?

**Đáp án gợi ý:** Quan hệ integration và quyền lực: ACL, customer-supplier, conformist, published language.

## 4. Domain Event khác Integration Event?

**Đáp án gợi ý:** Domain event nội bộ model; integration event là contract ổn định/versioned giữa context.

## 5. Aggregate quá lớn gây gì?

**Đáp án gợi ý:** Contention, load graph lớn, transaction dài và coupling.

## 6. ACL dùng khi nào?

**Đáp án gợi ý:** Bảo vệ model khỏi language/semantics legacy hoặc external system.

## 7. Eventual consistency cần gì?

**Đáp án gợi ý:** Convergence rule, idempotency, lag/SLO, reconciliation và UX expectation.

## 8. Evidence boundary tốt?

**Đáp án gợi ý:** Team ownership, change coupling thấp, language rõ và invariant không bị xuyên context tùy tiện.

## Cách sử dụng kết quả

- Nếu dưới 4 câu: quay lại diagram của **02 ddd boundaries**, chạy `demo.php` và ghi lại một misunderstanding cụ thể.
- Nếu đạt 4–6 câu: hoàn thành exercise, yêu cầu peer chỉ ra một failure path hoặc trade-off còn thiếu.
- Nếu đạt 7–8 câu: tự thiết kế một biến thể production của **02 ddd boundaries**, gồm test, metric và điều kiện rollback/revisit.
