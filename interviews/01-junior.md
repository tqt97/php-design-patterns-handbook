# Junior — Nền tảng và nhận diện

Bộ câu hỏi Junior kiểm tra khả năng nhận diện intent, đọc code before/after và giải thích bằng ví dụ PHP nhỏ. Câu trả lời tốt ưu tiên ngôn ngữ đơn giản, chỉ ra code smell và biết khi nào chưa cần pattern.

## 1. Design Pattern là gì và không phải là gì?

**Trả lời chi tiết:** Pattern là tên gọi chung cho một giải pháp đã lặp lại trong một bối cảnh, gồm intent, participants, forces và trade-off. Nó không phải code snippet, framework feature hay quy tắc bắt buộc.

**Cách ghi điểm:** Nêu vấn đề trước tên pattern; đưa một trường hợp không nên dùng.

**Câu hỏi đào sâu:** Với chủ đề **Design Pattern là gì và không phải là gì**, chọn một thay đổi yêu cầu cụ thể, mô tả giải pháp trực tiếp trước, rồi chỉ ra forces nào khiến pattern trở thành lựa chọn hợp lý. Giới hạn câu trả lời ở một ví dụ PHP nhỏ và nêu khi nào chưa cần pattern.

## 2. Vì sao `if/else` không tự động là code smell?

**Trả lời chi tiết:** Conditional đơn giản có thể rõ nhất khi số nhánh ít và ổn định. Nó trở thành smell khi nhánh tăng theo một change axis, logic lặp lại hoặc mỗi nhánh cần test/deploy độc lập.

**Cách ghi điểm:** Tránh câu trả lời “thấy switch là dùng Strategy”.

**Câu hỏi đào sâu:** Với chủ đề **Vì sao `if/else` không tự động là code smell**, đưa ra ngưỡng thực tế để giữ conditional và ngưỡng để tách policy; minh họa cách characterization test bảo vệ quyết định refactor. Giới hạn câu trả lời ở một ví dụ PHP nhỏ và nêu khi nào chưa cần pattern.

## 3. Strategy giải quyết thay đổi nào?

**Trả lời chi tiết:** Strategy đóng gói các thuật toán/policy cùng semantics sau một contract để caller chọn hoặc inject implementation mà không chứa logic từng biến thể.

**Cách ghi điểm:** So sánh với baseline `match` và nhắc chi phí wiring.

**Câu hỏi đào sâu:** Với chủ đề **Strategy giải quyết thay đổi nào**, xác định semantics chung giữa các strategy, cách chọn implementation và contract test ngăn hai policy trả kết quả không tương đương. Giới hạn câu trả lời ở một ví dụ PHP nhỏ và nêu khi nào chưa cần pattern.

## 4. Factory Method khác Simple Factory ra sao?

**Trả lời chi tiết:** Factory Method để creator/subclass quyết định product qua method override; Simple Factory thường là một object/function tập trung chọn concrete class.

**Cách ghi điểm:** Vẽ creator–product thay vì chỉ nói “Factory tạo object”.

**Câu hỏi đào sâu:** Với chủ đề **Factory Method khác Simple Factory ra sao**, vẽ Creator–Product, chỉ ra workflow thuộc Creator và giải thích khi nào một hàm `match` đơn giản dễ hiểu hơn Factory Method. Giới hạn câu trả lời ở một ví dụ PHP nhỏ và nêu khi nào chưa cần pattern.

## 5. Adapter cần map những gì?

**Trả lời chi tiết:** Không chỉ đổi tên method; adapter map request/response, unit, enum, timeout và exception của vendor sang contract/error nội bộ.

**Cách ghi điểm:** Nhắc contract test tại boundary.

**Câu hỏi đào sâu:** Với chủ đề **Adapter cần map những gì**, liệt kê request, response, unit, enum và error cần dịch; thiết kế contract test với timeout, malformed response và vendor decline. Giới hạn câu trả lời ở một ví dụ PHP nhỏ và nêu khi nào chưa cần pattern.

## 6. Decorator khác Proxy như thế nào?

**Trả lời chi tiết:** Cả hai bọc cùng contract. Decorator ghép thêm behavior; Proxy kiểm soát truy cập, lifecycle hoặc location của object.

**Cách ghi điểm:** Nêu ví dụ logging decorator và authorization proxy.

**Câu hỏi đào sâu:** Với chủ đề **Decorator khác Proxy như thế nào**, so sánh thứ tự wrapper, ownership của lifecycle và failure propagation; nêu một bug production do bọc sai thứ tự. Giới hạn câu trả lời ở một ví dụ PHP nhỏ và nêu khi nào chưa cần pattern.

## 7. Observer phù hợp khi nào?

**Trả lời chi tiết:** Khi một fact đã xảy ra cần nhiều reaction độc lập và publisher không nên biết concrete subscriber. Cần quyết định sync/async, ordering và error policy.

**Cách ghi điểm:** Nêu duplicate delivery nếu async.

**Câu hỏi đào sâu:** Với chủ đề **Observer phù hợp khi nào**, chọn sync hay async, định nghĩa delivery semantics và mô tả cách consumer xử lý duplicate/out-of-order event. Giới hạn câu trả lời ở một ví dụ PHP nhỏ và nêu khi nào chưa cần pattern.

## 8. State khác Strategy ở điểm nào?

**Trả lời chi tiết:** Strategy được client/chính sách chọn để đổi thuật toán; State thay behavior theo lifecycle và transition thường do context/state quyết định.

**Cách ghi điểm:** Dùng ví dụ shipping policy so với order lifecycle.

**Câu hỏi đào sâu:** Với chủ đề **State khác Strategy ở điểm nào**, xác định semantics chung giữa các strategy, cách chọn implementation và contract test ngăn hai policy trả kết quả không tương đương. Giới hạn câu trả lời ở một ví dụ PHP nhỏ và nêu khi nào chưa cần pattern.

## 9. Composition over inheritance có nghĩa cấm inheritance không?

**Trả lời chi tiết:** Không. Nó ưu tiên ghép behavior khi các trục thay đổi độc lập; inheritance vẫn phù hợp cho quan hệ is-a ổn định và tuân LSP.

**Cách ghi điểm:** Nêu subclass explosion là tín hiệu.

**Câu hỏi đào sâu:** Với chủ đề **Composition over inheritance có nghĩa cấm inheritance không**, đưa ra hai trục thay đổi độc lập gây subclass explosion và chứng minh composition giảm số tổ hợp phải duy trì. Giới hạn câu trả lời ở một ví dụ PHP nhỏ và nêu khi nào chưa cần pattern.

## 10. Singleton gây khó test vì sao?

**Trả lời chi tiết:** Dependency và lifecycle bị giấu sau global access, state tồn tại giữa test và khó thay fake. Container-managed singleton scope không nhất thiết là Singleton pattern nếu dependency được inject.

**Cách ghi điểm:** Phân biệt instance scope với global access.

**Câu hỏi đào sâu:** Với chủ đề **Singleton gây khó test vì sao**, phân biệt global access với container singleton scope; trình bày cách migrate sang constructor injection mà không big-bang. Giới hạn câu trả lời ở một ví dụ PHP nhỏ và nêu khi nào chưa cần pattern.

## 11. Interface có cần hai implementation không?

**Trả lời chi tiết:** Không bắt buộc. Interface có thể bảo vệ external boundary hoặc semantics ổn định; nhưng interface chỉ forward class nội bộ duy nhất có thể là abstraction sớm.

**Cách ghi điểm:** Nói rõ evidence cho seam.

**Câu hỏi đào sâu:** Với chủ đề **Interface có cần hai implementation không**, chỉ ra boundary nào cần contract ổn định, evidence cho fake/alternate implementation và cleanup condition nếu interface chỉ forward. Giới hạn câu trả lời ở một ví dụ PHP nhỏ và nêu khi nào chưa cần pattern.

## 12. Pattern nào hiện diện trong middleware?

**Trả lời chi tiết:** Middleware thường tạo Chain of Responsibility/Pipeline: request đi qua ordered handlers, có thể transform hoặc short-circuit.

**Cách ghi điểm:** Nhắc ordering là một phần contract.

**Câu hỏi đào sâu:** Với chủ đề **Pattern nào hiện diện trong middleware**, phân tích ordering, short-circuit, cleanup và error propagation; nêu test bảo vệ semantics của chuỗi xử lý. Giới hạn câu trả lời ở một ví dụ PHP nhỏ và nêu khi nào chưa cần pattern.

## 13. Builder hữu ích khi nào?

**Trả lời chi tiết:** Khi object có nhiều bước cấu hình, invariant cuối hoặc nhiều representation; builder gom quá trình tạo và validate khi build.

**Cách ghi điểm:** Không dùng builder chỉ để thay constructor 3 tham số.

**Câu hỏi đào sâu:** Với chủ đề **Builder hữu ích khi nào**, xác định invariant chỉ kiểm tra được khi build, representation nào thay đổi và vì sao constructor/named constructor chưa đủ. Giới hạn câu trả lời ở một ví dụ PHP nhỏ và nêu khi nào chưa cần pattern.

## 14. Facade khác Adapter?

**Trả lời chi tiết:** Facade cung cấp API đơn giản cho subsystem có contract vốn dùng được; Adapter làm contract không tương thích trở nên phù hợp target.

**Cách ghi điểm:** Nêu “simplify” so với “translate”.

**Câu hỏi đào sâu:** Với chủ đề **Facade khác Adapter**, liệt kê request, response, unit, enum và error cần dịch; thiết kế contract test với timeout, malformed response và vendor decline. Giới hạn câu trả lời ở một ví dụ PHP nhỏ và nêu khi nào chưa cần pattern.

## 15. Làm sao nhận biết over-engineering?

**Trả lời chi tiết:** Abstraction không gắn change axis, wrapper chỉ forward, flow khó trace, test không dễ hơn và không có owner/revisit condition.

**Cách ghi điểm:** Đề xuất xóa hoặc inline để so sánh.

**Câu hỏi đào sâu:** Với chủ đề **Làm sao nhận biết over-engineering**, chọn một abstraction thật, liệt kê cost of change/trace/test và đề xuất experiment inline/xóa để so sánh evidence. Giới hạn câu trả lời ở một ví dụ PHP nhỏ và nêu khi nào chưa cần pattern.