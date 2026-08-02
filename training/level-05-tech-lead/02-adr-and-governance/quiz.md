# Quiz — 02 Adr And Governance

## 1. ADR cần chứa gì?

**Đáp án gợi ý:** Context, drivers, alternatives, decision, consequences, rollout, verification, revisit.

## 2. ADR không nên là gì?

**Đáp án gợi ý:** Biên bản họp dài hoặc tuyên bố “best practice” không alternatives/evidence.

## 3. Fitness function là gì?

**Đáp án gợi ý:** Kiểm tra tự động một constraint kiến trúc như forbidden dependency/schema compatibility.

## 4. Exception governance?

**Đáp án gợi ý:** Có lý do, owner, scope, expiry và remediation; không biến rule thành tuyệt đối vô nghĩa.

## 5. Revisit trigger ví dụ?

**Đáp án gợi ý:** Traffic/latency/domain complexity/team ownership hoặc vendor capability thay đổi.

## 6. Decision reversible xử lý sao?

**Đáp án gợi ý:** Thử nghiệm nhỏ/timebox; giảm ceremony so với quyết định khó đảo.

## 7. Traceability?

**Đáp án gợi ý:** ADR link source, tests, dashboard, runbook và incident feedback.

## 8. Đo governance hiệu quả?

**Đáp án gợi ý:** Lead time, escaped architecture violations, exception aging và decision outcomes—not số ADR.

## Cách sử dụng kết quả

- Nếu dưới 4 câu: quay lại diagram của **02 adr and governance**, chạy `demo.php` và ghi lại một misunderstanding cụ thể.
- Nếu đạt 4–6 câu: hoàn thành exercise, yêu cầu peer chỉ ra một failure path hoặc trade-off còn thiếu.
- Nếu đạt 7–8 câu: tự thiết kế một biến thể production của **02 adr and governance**, gồm test, metric và điều kiện rollback/revisit.
