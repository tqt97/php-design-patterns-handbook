# Quiz — 02 Solid In Practice

## 1. SRP được đo bằng gì?

**Đáp án gợi ý:** Số lý do thay đổi và ownership, không phải số method; class có thể dài nhưng vẫn một trách nhiệm.

## 2. OCP có yêu cầu interface cho mọi class?

**Đáp án gợi ý:** Không. Chỉ tạo extension point khi variation/change axis có evidence; function/composition nhỏ có thể đủ.

## 3. Ví dụ vi phạm LSP?

**Đáp án gợi ý:** Subtype ném lỗi cho operation hợp lệ của base contract hoặc làm mạnh precondition/yếu postcondition.

## 4. ISP giải quyết pain nào?

**Đáp án gợi ý:** Client bị ép phụ thuộc method không dùng, fake/test phải cài hành vi vô nghĩa và thay đổi interface ảnh hưởng quá rộng.

## 5. DIP khác DI thế nào?

**Đáp án gợi ý:** DIP là hướng dependency về policy/abstraction; DI là kỹ thuật cung cấp dependency. Container không tự tạo ra DIP.

## 6. Khi nào SOLID gây over-engineering?

**Đáp án gợi ý:** Khi áp dụng theo checklist, tạo nhiều interface một implementation và không có scenario thay đổi/test seam.

## 7. Test nào hỗ trợ OCP?

**Đáp án gợi ý:** Contract test chạy cho mọi implementation và extension test thêm biến thể không sửa client.

## 8. Cách review SOLID có chất lượng?

**Đáp án gợi ý:** Hỏi change driver, blast radius, substitutability và chi phí, không đếm class/interface.

## Cách sử dụng kết quả

- Nếu dưới 4 câu: quay lại diagram của **02 solid in practice**, chạy `demo.php` và ghi lại một misunderstanding cụ thể.
- Nếu đạt 4–6 câu: hoàn thành exercise, yêu cầu peer chỉ ra một failure path hoặc trade-off còn thiếu.
- Nếu đạt 7–8 câu: tự thiết kế một biến thể production của **02 solid in practice**, gồm test, metric và điều kiện rollback/revisit.
