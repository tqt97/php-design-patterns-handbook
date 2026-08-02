# Quiz — 03 Refactoring Safety

## 1. Characterization test dùng để làm gì?

**Đáp án gợi ý:** Khóa behavior quan sát được của code cũ, kể cả behavior chưa đẹp, trước khi thay cấu trúc.

## 2. Seam là gì?

**Đáp án gợi ý:** Điểm có thể thay implementation/đưa test double mà không sửa toàn workflow, ví dụ function boundary hoặc interface nhỏ.

## 3. Parallel Change gồm bước nào?

**Đáp án gợi ý:** Expand contract, migrate caller/data, observe, rồi contract/xóa đường cũ.

## 4. Vì sao commit nhỏ quan trọng?

**Đáp án gợi ý:** Dễ review, bisect, rollback và tách lỗi behavior khỏi thay đổi cấu trúc.

## 5. Golden Master phù hợp khi nào?

**Đáp án gợi ý:** Output lớn/legacy khó mô tả; phải kiểm soát nondeterminism và review snapshot có ý nghĩa.

## 6. Refactor có được đổi behavior không?

**Đáp án gợi ý:** Không trong cùng bước; feature change nên tách commit/test để giữ khả năng chẩn đoán.

## 7. Evidence nào cho phép xóa code cũ?

**Đáp án gợi ý:** Traffic migrated, shadow mismatch trong ngưỡng, metric ổn, rollback window qua và owner xác nhận.

## 8. Cách ghi điểm khi kể refactor?

**Đáp án gợi ý:** Nêu risk, safety net, seam, sequence, rollback và kết quả đo được.

## Cách sử dụng kết quả

- Nếu dưới 4 câu: quay lại diagram của **03 refactoring safety**, chạy `demo.php` và ghi lại một misunderstanding cụ thể.
- Nếu đạt 4–6 câu: hoàn thành exercise, yêu cầu peer chỉ ra một failure path hoặc trade-off còn thiếu.
- Nếu đạt 7–8 câu: tự thiết kế một biến thể production của **03 refactoring safety**, gồm test, metric và điều kiện rollback/revisit.
