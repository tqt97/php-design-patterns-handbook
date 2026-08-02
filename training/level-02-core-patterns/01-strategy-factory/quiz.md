# Quiz — 01 Strategy Factory

## 1. Strategy cô lập điều gì?

**Đáp án gợi ý:** Một family thuật toán/policy cùng contract; caller không chứa công thức từng biến thể.

## 2. Factory chịu trách nhiệm gì?

**Đáp án gợi ý:** Ownership của creation/selection tại composition boundary, không chứa business calculation của Strategy.

## 3. Strategy khác State ở ownership lựa chọn?

**Đáp án gợi ý:** Strategy thường do caller/config chọn; State chuyển theo lifecycle nội bộ của context.

## 4. Khi nào `match` tốt hơn Factory?

**Đáp án gợi ý:** Ít product ổn định, creation đơn giản, không có lifecycle/dependency phức tạp.

## 5. Contract test Strategy kiểm tra gì?

**Đáp án gợi ý:** Pre/postcondition chung, units/rounding, error semantics cho mọi policy.

## 6. Selection theo tenant cần lưu gì?

**Đáp án gợi ý:** Policy key/version/cohort để audit, rollback và tái hiện kết quả.

## 7. Failure demo quan trọng?

**Đáp án gợi ý:** Selector drift hoặc policy trả kết quả khác semantics; shadow compare giúp phát hiện trước rollout.

## 8. Trade-off chính?

**Đáp án gợi ý:** Thêm type/wiring/registry; lợi ích chỉ có khi policy thực sự thay đổi độc lập.

## Cách sử dụng kết quả

- Nếu dưới 4 câu: quay lại diagram của **01 strategy factory**, chạy `demo.php` và ghi lại một misunderstanding cụ thể.
- Nếu đạt 4–6 câu: hoàn thành exercise, yêu cầu peer chỉ ra một failure path hoặc trade-off còn thiếu.
- Nếu đạt 7–8 câu: tự thiết kế một biến thể production của **01 strategy factory**, gồm test, metric và điều kiện rollback/revisit.
