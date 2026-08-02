# Quiz — 01 Oop And Object Collaboration

## 1. Object nào nên sở hữu invariant của Order total?

**Đáp án gợi ý:** Order hoặc value object gần dữ liệu phải bảo vệ invariant; service không nên ghép getter/setter rồi hy vọng caller làm đúng.

## 2. Tell, Don’t Ask giúp gì trong case này?

**Đáp án gợi ý:** Caller gửi intent như `addLine()`/`confirm()` thay vì lấy state ra tự tính; logic và invariant ở cùng object.

## 3. Dấu hiệu Anemic Domain Model là gì?

**Đáp án gợi ý:** Entity chỉ có getter/setter, còn mọi quyết định nằm trong service lớn; thay đổi rule lan qua nhiều caller.

## 4. Khi nào DTO vẫn phù hợp?

**Đáp án gợi ý:** Ở boundary truyền dữ liệu, serialization hoặc read projection; DTO không thay entity/value object sở hữu behavior.

## 5. Test nào chứng minh collaboration tốt hơn?

**Đáp án gợi ý:** Behavior test gọi public intent và assert state/result, không set private state hay mock mọi object.

## 6. Một object nên biết bao nhiêu dependency?

**Đáp án gợi ý:** Chỉ collaborator cần cho trách nhiệm của nó; nhiều dependency khác loại thường báo hiệu cohesion thấp.

## 7. Vì sao không đặt mọi behavior trong entity?

**Đáp án gợi ý:** I/O, orchestration và cross-aggregate workflow thuộc application/domain service phù hợp; entity giữ invariant cục bộ.

## 8. Cách ghi điểm khi giải thích OOP?

**Đáp án gợi ý:** Nêu ownership, invariant, message flow và change scenario cụ thể thay vì chỉ nói encapsulation/inheritance.

## Cách sử dụng kết quả

- Nếu dưới 4 câu: quay lại diagram của **01 oop and object collaboration**, chạy `demo.php` và ghi lại một misunderstanding cụ thể.
- Nếu đạt 4–6 câu: hoàn thành exercise, yêu cầu peer chỉ ra một failure path hoặc trade-off còn thiếu.
- Nếu đạt 7–8 câu: tự thiết kế một biến thể production của **01 oop and object collaboration**, gồm test, metric và điều kiện rollback/revisit.
