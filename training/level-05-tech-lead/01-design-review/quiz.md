# Quiz — 01 Design Review

## 1. Review abstraction bắt đầu từ đâu?

**Đáp án gợi ý:** Problem/invariant/change driver, không bắt đầu class diagram.

## 2. Evidence tối thiểu?

**Đáp án gợi ý:** Baseline, alternatives, tests, failure path, migration/rollback và metric nếu production.

## 3. Comment review tốt?

**Đáp án gợi ý:** Nêu risk, impact, evidence thiếu và action; tránh “tôi thích pattern X”.

## 4. Hidden coupling thường ở đâu?

**Đáp án gợi ý:** Lifecycle, transaction, shared DB, event schema, ordering và operational ownership.

## 5. Khi yêu cầu ADR?

**Đáp án gợi ý:** Decision khó đảo, cross-team, có trade-off/constraint lâu dài hoặc cần revisit trigger.

## 6. Review diagram thế nào?

**Đáp án gợi ý:** Participant khớp code, arrows có direction/timing, failure và source of truth xuất hiện.

## 7. Cách tránh governance nặng?

**Đáp án gợi ý:** Automate rule ổn định; exception process nhẹ, có owner/expiry.

## 8. Cách coaching?

**Đáp án gợi ý:** Dùng câu hỏi và counterexample, yêu cầu candidate tự so baseline/evidence.

## Cách sử dụng kết quả

- Nếu dưới 4 câu: quay lại diagram của **01 design review**, chạy `demo.php` và ghi lại một misunderstanding cụ thể.
- Nếu đạt 4–6 câu: hoàn thành exercise, yêu cầu peer chỉ ra một failure path hoặc trade-off còn thiếu.
- Nếu đạt 7–8 câu: tự thiết kế một biến thể production của **01 design review**, gồm test, metric và điều kiện rollback/revisit.
