# Quiz — 01 Clean Hexagonal

## 1. Inbound port là gì?

**Đáp án gợi ý:** Use-case contract mà entrypoint gọi; không phải HTTP controller interface.

## 2. Outbound port là gì?

**Đáp án gợi ý:** Contract application/domain cần để tương tác DB/provider/time.

## 3. Dependency direction?

**Đáp án gợi ý:** Adapter phụ thuộc port/policy; policy không import framework/infrastructure.

## 4. Composition root nằm đâu?

**Đáp án gợi ý:** Ngoài core, nơi lắp concrete adapter theo runtime/entrypoint.

## 5. Error translation ở đâu?

**Đáp án gợi ý:** Adapter map technical error; application map thành outcome use case ổn định.

## 6. Test pyramid theo boundary?

**Đáp án gợi ý:** Domain/use-case unit, port contract, adapter integration, ít E2E journey.

## 7. Folder structure có đủ không?

**Đáp án gợi ý:** Không; cần import/dependency rule và ownership semantics.

## 8. Khi architecture quá mức?

**Đáp án gợi ý:** CRUD nhỏ, ít boundary/change; layering tăng navigation mà không giảm risk.

## Cách sử dụng kết quả

- Nếu dưới 4 câu: quay lại diagram của **01 clean hexagonal**, chạy `demo.php` và ghi lại một misunderstanding cụ thể.
- Nếu đạt 4–6 câu: hoàn thành exercise, yêu cầu peer chỉ ra một failure path hoặc trade-off còn thiếu.
- Nếu đạt 7–8 câu: tự thiết kế một biến thể production của **01 clean hexagonal**, gồm test, metric và điều kiện rollback/revisit.
