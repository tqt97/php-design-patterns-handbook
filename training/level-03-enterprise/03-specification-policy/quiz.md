# Quiz — 03 Specification Policy

## 1. Specification biểu diễn gì?

**Đáp án gợi ý:** Rule/predicate có tên, có thể compose và trả reason/explanation nếu cần.

## 2. Policy khác Specification?

**Đáp án gợi ý:** Specification trả eligibility/boolean; policy có thể chọn/tính decision/value.

## 3. AND/OR cần test gì?

**Đáp án gợi ý:** Truth table, short-circuit nếu là contract, reason combination và property tests.

## 4. Rule order có ý nghĩa không?

**Đáp án gợi ý:** Nếu có precedence/stacking, phải mô hình hóa rõ thay vì dựa thứ tự list ngầm.

## 5. Predicate inline khi nào tốt?

**Đáp án gợi ý:** Rule nhỏ, dùng một nơi, không cần explanation/composition.

## 6. Version rule để làm gì?

**Đáp án gợi ý:** Tái hiện decision lịch sử, audit và rollout/rollback campaign.

## 7. Failure thường gặp?

**Đáp án gợi ý:** Object graph quá sâu, reason mất, rule có I/O và nondeterministic.

## 8. Evidence?

**Đáp án gợi ý:** Truth table, decision examples, rejection metrics và shadow evaluation.

## Cách sử dụng kết quả

- Nếu dưới 4 câu: quay lại diagram của **03 specification policy**, chạy `demo.php` và ghi lại một misunderstanding cụ thể.
- Nếu đạt 4–6 câu: hoàn thành exercise, yêu cầu peer chỉ ra một failure path hoặc trade-off còn thiếu.
- Nếu đạt 7–8 câu: tự thiết kế một biến thể production của **03 specification policy**, gồm test, metric và điều kiện rollback/revisit.
