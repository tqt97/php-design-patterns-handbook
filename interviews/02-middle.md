# Middle — Refactoring và collaboration

Bộ câu hỏi Middle đánh giá khả năng refactor an toàn, so sánh pattern gần nhau, viết contract test và phân tích chi phí abstraction trong một module ứng dụng.

## 1. Refactor switch sang Strategy an toàn thế nào?

**Trả lời chi tiết:** Tạo characterization tests, xác định semantics chung, extract từng nhánh sau contract, so sánh output/failure và chuyển selection ra composition boundary.

**Cách ghi điểm:** Nêu rollback và không trộn refactor với đổi behavior.

**Câu hỏi đào sâu:** Với chủ đề **Refactor switch sang Strategy an toàn thế nào**, xác định semantics chung giữa các strategy, cách chọn implementation và contract test ngăn hai policy trả kết quả không tương đương. Bổ sung contract test, change axis và chi phí wiring trong một module đang bảo trì.

## 2. Strategy và Template Method nên chọn cái nào?

**Trả lời chi tiết:** Strategy dùng composition và đổi algorithm runtime; Template Method dùng inheritance, giữ skeleton ổn định và subclass override steps. Chọn theo trục thay đổi và quyền kiểm soát lifecycle.

**Cách ghi điểm:** Đề cập testability và subclass coupling.

**Câu hỏi đào sâu:** Với chủ đề **Strategy và Template Method nên chọn cái nào**, xác định semantics chung giữa các strategy, cách chọn implementation và contract test ngăn hai policy trả kết quả không tương đương. Bổ sung contract test, change axis và chi phí wiring trong một module đang bảo trì.

## 3. Decorator order có ý nghĩa gì?

**Trả lời chi tiết:** Có. Validation→retry→logging khác retry→validation; wrapper order ảnh hưởng side effect, metric và số lần gọi. Order phải được test như contract.

**Cách ghi điểm:** Dùng call-count test.

**Câu hỏi đào sâu:** Với chủ đề **Decorator order có ý nghĩa gì**, so sánh thứ tự wrapper, ownership của lifecycle và failure propagation; nêu một bug production do bọc sai thứ tự. Bổ sung contract test, change axis và chi phí wiring trong một module đang bảo trì.

## 4. Observer đồng bộ thất bại thì transaction xử lý sao?

**Trả lời chi tiết:** Phải quyết định listener thuộc invariant hay side effect. Listener critical có thể cùng transaction; side effect nên outbox/async để tránh rollback business state vì email lỗi.

**Cách ghi điểm:** Phân loại critical và best-effort.

**Câu hỏi đào sâu:** Với chủ đề **Observer đồng bộ thất bại thì transaction xử lý sao**, chọn sync hay async, định nghĩa delivery semantics và mô tả cách consumer xử lý duplicate/out-of-order event. Bổ sung contract test, change axis và chi phí wiring trong một module đang bảo trì.

## 5. Repository có cần cho mọi Eloquent model?

**Trả lời chi tiết:** Không. Repository có giá trị cho aggregate collection, persistence complexity hoặc boundary; CRUD mỏng trên Active Record thường thêm ceremony mà không che semantics.

**Cách ghi điểm:** So sánh với Query Object cho reporting.

**Câu hỏi đào sâu:** Với chủ đề **Repository có cần cho mọi Eloquent model**, định nghĩa collection semantics, aggregate boundary và transaction expectation; so sánh với Eloquent/query trực tiếp bằng một use case. Bổ sung contract test, change axis và chi phí wiring trong một module đang bảo trì.

## 6. Query Object khác Specification?

**Trả lời chi tiết:** Query Object đóng gói cách lấy projection/read data; Specification biểu diễn predicate/business rule có thể compose và dùng cả in-memory/domain.

**Cách ghi điểm:** Nêu nguy cơ Specification phụ thuộc ORM.

**Câu hỏi đào sâu:** Với chủ đề **Query Object khác Specification**, thiết kế criteria, pagination/cursor và projection; nêu cách test query semantics mà không biến Query Object thành generic repository. Bổ sung contract test, change axis và chi phí wiring trong một module đang bảo trì.

## 7. Unit of Work giải quyết gì?

**Trả lời chi tiết:** Theo dõi thay đổi và commit nhiều object như một transaction; cần rollback, nested semantics và integration với outbox. ORM có thể đã cung cấp UoW.

**Cách ghi điểm:** Không tự xây nếu ORM đáp ứng.

**Câu hỏi đào sâu:** Với chủ đề **Unit of Work giải quyết gì**, xác định atomic boundary, commit order, rollback và outbox; mô phỏng crash giữa persistence và message publish. Bổ sung contract test, change axis và chi phí wiring trong một module đang bảo trì.

## 8. Command khác Event?

**Trả lời chi tiết:** Command thể hiện ý định, có một handler logic và có thể bị từ chối; Event là fact quá khứ, có nhiều subscriber và không “undo” bằng cách từ chối.

**Cách ghi điểm:** Tên command mệnh lệnh, event quá khứ.

**Câu hỏi đào sâu:** Với chủ đề **Command khác Event**, phân biệt intent và fact, ownership của retry, versioning payload và cách tránh dùng event như RPC ẩn. Bổ sung contract test, change axis và chi phí wiring trong một module đang bảo trì.

## 9. Chain khác Pipeline?

**Trả lời chi tiết:** Chain thường cho handler quyết định xử lý/forward; Pipeline nhấn mạnh tuần tự transform qua các stage. Trong thực tế có thể overlap, nên contract và short-circuit quan trọng hơn tên.

**Cách ghi điểm:** Mô tả input/output semantics.

**Câu hỏi đào sâu:** Với chủ đề **Chain khác Pipeline**, phân tích ordering, short-circuit, cleanup và error propagation; nêu test bảo vệ semantics của chuỗi xử lý. Bổ sung contract test, change axis và chi phí wiring trong một module đang bảo trì.

## 10. Adapter contract test gồm gì?

**Trả lời chi tiết:** Happy mapping, unit/enum conversion, timeout/error translation, malformed response, idempotency/retry safety và fixtures theo phiên bản provider.

**Cách ghi điểm:** Không mock chính adapter trong contract test.

**Câu hỏi đào sâu:** Với chủ đề **Adapter contract test gồm gì**, liệt kê request, response, unit, enum và error cần dịch; thiết kế contract test với timeout, malformed response và vendor decline. Bổ sung contract test, change axis và chi phí wiring trong một module đang bảo trì.

## 11. State machine nên lưu state ở đâu?

**Trả lời chi tiết:** State hiện tại thuộc aggregate/source of truth; transition logic ở aggregate/state objects. Side effect sau transition nên event/outbox.

**Cách ghi điểm:** Nhắc optimistic locking.

**Câu hỏi đào sâu:** Với chủ đề **State machine nên lưu state ở đâu**, vẽ transition table, chỉ ra illegal transition và xác định aggregate hay state object sở hữu quyền chuyển trạng thái. Bổ sung contract test, change axis và chi phí wiring trong một module đang bảo trì.

## 12. Factory phình to với nhiều `match` phải làm gì?

**Trả lời chi tiết:** Xem selection là config/registry, plugin discovery hay concrete creator ownership. Không nhất thiết chuyển sang Abstract Factory nếu product families không tồn tại.

**Cách ghi điểm:** Đo complexity và ownership.

**Câu hỏi đào sâu:** Với chủ đề **Factory phình to với nhiều `match` phải làm gì**, với “Factory phình to với nhiều `match` phải làm gì”, mô tả change axis, contract test, failure path và chi phí wiring mà team phải chấp nhận. Bổ sung contract test, change axis và chi phí wiring trong một module đang bảo trì.

## 13. Builder làm sao giữ invariant?

**Trả lời chi tiết:** Các step thu thập dữ liệu; `build()` validate cross-field invariant và trả object hợp lệ. Có thể dùng staged builder nếu compile-time sequence thật sự có giá trị.

**Cách ghi điểm:** Tránh object half-valid thoát ra.

**Câu hỏi đào sâu:** Với chủ đề **Builder làm sao giữ invariant**, xác định invariant chỉ kiểm tra được khi build, representation nào thay đổi và vì sao constructor/named constructor chưa đủ. Bổ sung contract test, change axis và chi phí wiring trong một module đang bảo trì.

## 14. Facade có thể trở thành God Service thế nào?

**Trả lời chi tiết:** Khi facade sở hữu business rule, state và quá nhiều use case thay vì chỉ phối hợp subsystem. Tách application services và giữ facade mỏng theo capability.

**Cách ghi điểm:** Kiểm tra số reason-to-change.

**Câu hỏi đào sâu:** Với chủ đề **Facade có thể trở thành God Service thế nào**, chỉ ra subsystem nào được đơn giản hóa, phần nào không được che giấu và cách ngăn Facade biến thành God Service. Bổ sung contract test, change axis và chi phí wiring trong một module đang bảo trì.

## 15. Làm sao test pattern mà không test implementation detail?

**Trả lời chi tiết:** Test observable contract/invariant và failure behavior; contract tests cho implementations; chỉ test collaboration khi interaction là semantics như “publish once after commit”.

**Cách ghi điểm:** Không assert tên concrete class vô ích.

**Câu hỏi đào sâu:** Với chủ đề **Làm sao test pattern mà không test implementation detail**, với “Làm sao test pattern mà không test implementation detail”, mô tả change axis, contract test, failure path và chi phí wiring mà team phải chấp nhận. Bổ sung contract test, change axis và chi phí wiring trong một module đang bảo trì.