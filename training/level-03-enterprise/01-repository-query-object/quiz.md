# Quiz — 01 Repository Query Object

## 1. Repository contract nên dùng ngôn ngữ gì?

**Đáp án gợi ý:** Aggregate/domain intent như `ofId`, `save`, không expose Query Builder/SQL.

## 2. Query Object trả gì?

**Đáp án gợi ý:** Projection/read model phù hợp use case, có filter/order/page semantics rõ.

## 3. Vì sao không dùng Repository cho report?

**Đáp án gợi ý:** Aggregate loading gây over-fetch/N+1 và trộn write semantics với read performance.

## 4. Contract test Repository kiểm tra gì?

**Đáp án gợi ý:** Save/get, identity, not-found, version/conflict semantics trên mọi adapter.

## 5. Cursor cần thuộc tính gì?

**Đáp án gợi ý:** Stable order và unique tie-breaker; semantics khi record mới chèn phải công bố.

## 6. Eloquent trực tiếp khi nào tốt?

**Đáp án gợi ý:** CRUD/read nhỏ, không có domain collection semantics hay persistence isolation cần thiết.

## 7. Read replica gây vấn đề gì?

**Đáp án gợi ý:** Lag/read-your-write; use case phải biết consistency expectation.

## 8. Evidence production?

**Đáp án gợi ý:** Query plan, latency p95, rows examined, query count và correctness dataset.

## Cách sử dụng kết quả

- Nếu dưới 4 câu: quay lại diagram của **01 repository query object**, chạy `demo.php` và ghi lại một misunderstanding cụ thể.
- Nếu đạt 4–6 câu: hoàn thành exercise, yêu cầu peer chỉ ra một failure path hoặc trade-off còn thiếu.
- Nếu đạt 7–8 câu: tự thiết kế một biến thể production của **01 repository query object**, gồm test, metric và điều kiện rollback/revisit.
