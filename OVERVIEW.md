# Design Patterns Overview

Tài liệu này là bản đồ điều hướng trung tâm: ma trận lựa chọn pattern, phương pháp học và liên kết đến nội dung liên quan trong repository. Ma trận không thay thế phân tích context; hãy chọn pattern từ **lực thay đổi và invariant**, không từ từ khóa.

## Phương pháp học

### Vòng học 5 bước

1. **Nhận diện vấn đề:** đọc code trước, liệt kê coupling, conditional, lifecycle hoặc boundary đang thay đổi.
2. **Dự đoán thiết kế:** tự vẽ dependency trước khi đọc lời giải.
3. **Chạy và kiểm thử:** chạy example/playground; thêm test cho invariant và failure path.
4. **So sánh lựa chọn:** ghi ít nhất một giải pháp trực tiếp và một pattern lân cận.
5. **Ghi quyết định:** dùng ADR khi lựa chọn ảnh hưởng public API, persistence, consistency hoặc team convention.

### Tuyến học theo cấp độ

| Level | Mục tiêu | Tài liệu |
|---|---|---|
| Junior | OOP, SOLID, dependency và pattern phổ biến | [Foundations](docs/00-foundations/README.md), [Creational](docs/01-creational/README.md), [Structural](docs/02-structural/README.md) |
| Middle | Refactor, behavioral pattern, testing và framework | [Behavioral](docs/03-behavioral/README.md), [Examples](examples/README.md), [Labs](labs/README.md) |
| Senior | Enterprise, consistency, architecture và production | [Enterprise](docs/04-enterprise-patterns/README.md), [Handbook](handbook/README.md), [Production](production/README.md) |
| Tech Lead | ADR, governance, training và interview | [Decisions](decisions/README.md), [Training](training/README.md), [Interviews](interviews/README.md) |

## Ma trận Design Pattern

| Pattern | Nhóm | Ý định | Tín hiệu phù hợp | Dễ nhầm với | Bài chính |
|---|---|---|---|---|---|
| Factory Method | Creational | Ủy quyền tạo product cho creator/subclass | Loại product thay đổi theo workflow | Simple Factory, Abstract Factory | [Factory Method](docs/01-creational/01-factory-method.md) |
| Abstract Factory | Creational | Tạo một họ object tương thích | Nhiều family và tính tương thích là invariant | Factory Method, Builder | [Abstract Factory](docs/01-creational/02-abstract-factory.md) |
| Builder | Creational | Dựng object phức tạp theo bước | Nhiều cấu hình nhưng chỉ một số tổ hợp hợp lệ | Factory Method | [Builder](docs/01-creational/03-builder.md) |
| Prototype | Creational | Tạo object bằng clone mẫu | Khởi tạo đắt hoặc cấu hình mẫu lặp lại | Builder | [Prototype](docs/01-creational/04-prototype.md) |
| Singleton | Creational | Một instance theo scope | Resource thật sự cần lifecycle dùng chung | Container scope | [Singleton](docs/01-creational/05-singleton.md) |
| Adapter | Structural | Dịch contract không tương thích | Tích hợp vendor/legacy mà domain không nên biết | Facade, Anti-Corruption Layer | [Adapter](docs/02-structural/01-adapter.md) |
| Bridge | Structural | Tách hai trục thay đổi độc lập | Hai hierarchy tăng độc lập và tránh tích subclass | Strategy, Adapter | [Bridge](docs/02-structural/02-bridge.md) |
| Composite | Structural | Xử lý leaf và group qua cùng contract | Cấu trúc cây và phép toán đệ quy | Decorator | [Composite](docs/02-structural/03-composite.md) |
| Decorator | Structural | Ghép hành vi bằng wrapper cùng contract | Logging/retry/cache cần tổ hợp runtime | Proxy | [Decorator](docs/02-structural/04-decorator.md) |
| Facade | Structural | API đơn giản cho subsystem | Client không cần biết orchestration nội bộ | Adapter, Service Layer | [Facade](docs/02-structural/05-facade.md) |
| Flyweight | Structural | Chia sẻ intrinsic state | Số lượng object rất lớn và state lặp | Object Pool | [Flyweight](docs/02-structural/06-flyweight.md) |
| Proxy | Structural | Kiểm soát truy cập subject | Authorization, lazy, remote hoặc cache transparent | Decorator | [Proxy](docs/02-structural/07-proxy.md) |
| Chain of Responsibility | Behavioral | Chuyển request qua chuỗi handler | Rule độc lập, có short-circuit/order | Pipeline | [Chain of Responsibility](docs/03-behavioral/01-chain-of-responsibility.md) |
| Command | Behavioral | Đóng gói ý định thành object | Queue, audit, undo hoặc dispatch use case | Strategy, Job | [Command](docs/03-behavioral/02-command.md) |
| Interpreter | Behavioral | Biểu diễn grammar và evaluate expression | DSL nhỏ, grammar ổn định | Parser library | [Interpreter](docs/03-behavioral/03-interpreter.md) |
| Iterator | Behavioral | Duyệt collection không lộ cấu trúc | Nhiều traversal hoặc lazy sequence | Generator | [Iterator](docs/03-behavioral/04-iterator.md) |
| Mediator | Behavioral | Điều phối collaboration qua trung tâm | Nhiều component liên kết chéo | Observer | [Mediator](docs/03-behavioral/05-mediator.md) |
| Memento | Behavioral | Chụp và phục hồi state | Undo/rollback state cục bộ | Event Sourcing | [Memento](docs/03-behavioral/06-memento.md) |
| Observer | Behavioral | Thông báo subscriber khi event xảy ra | Nhiều phản ứng độc lập với fact | Mediator, Event Bus | [Observer](docs/03-behavioral/07-observer.md) |
| State | Behavioral | Hành vi thay đổi theo lifecycle state | Transition và rule phụ thuộc trạng thái | Strategy | [State](docs/03-behavioral/08-state.md) |
| Strategy | Behavioral | Thay algorithm/policy qua contract | Nhiều cách tính/chọn có cùng output contract | State, Template Method | [Strategy](docs/03-behavioral/09-strategy.md) |
| Template Method | Behavioral | Giữ skeleton, mở một số hook | Quy trình ổn định nhưng vài bước thay đổi | Strategy | [Template Method](docs/03-behavioral/10-template-method.md) |
| Visitor | Behavioral | Thêm operation lên object structure | Node types ổn định, operation tăng | Pattern Matching | [Visitor](docs/03-behavioral/11-visitor.md) |
| Repository | Enterprise | Collection-like persistence boundary | Domain write model cần độc lập ORM | Query Object, Active Record | [Repository](docs/04-enterprise-patterns/01-repository.md) |
| Service Layer | Enterprise | Application boundary theo use case | Điều phối transaction/domain/integration | Facade | [Service Layer](docs/04-enterprise-patterns/02-service-layer.md) |
| Query Object | Enterprise | Đóng gói truy vấn/read model | Filter/projection/pagination phức tạp | Repository | [Query Object](docs/04-enterprise-patterns/03-query-object.md) |
| Specification | Enterprise | Đặt tên và kết hợp business predicate | Rule dùng lại/kết hợp cần ngôn ngữ domain | Policy, Validator | [Specification](docs/04-enterprise-patterns/04-specification.md) |
| Unit of Work | Enterprise | Theo dõi thay đổi và commit transaction | Nhiều object phải atomic | Transaction Script | [Unit of Work](docs/04-enterprise-patterns/05-unit-of-work.md) |
| Data Mapper | Enterprise | Tách domain object khỏi persistence | Domain model giàu cần độc lập schema | Active Record | [Data Mapper](docs/04-enterprise-patterns/06-data-mapper.md) |
| Active Record | Enterprise | Record tự persistence | CRUD đơn giản, domain logic vừa phải | Data Mapper | [Active Record](docs/04-enterprise-patterns/07-active-record.md) |

## Ma trận chọn nhanh theo vấn đề

| Vấn đề chính | Bắt đầu xem | Câu hỏi kiểm tra |
|---|---|---|
| Client đang tự chọn thuật toán | Strategy | Có nhiều policy cùng contract hay chỉ một `if` đơn giản? |
| Client biết concrete class khi tạo object | Factory Method / Abstract Factory | Creation có thực sự thay đổi độc lập không? |
| Constructor/configuration khó bảo đảm hợp lệ | Builder | Có nhiều bước hoặc tổ hợp invalid không? |
| Vendor API lệch domain contract | Adapter | Cần dịch cả dữ liệu, exception và semantics nào? |
| Hai trục subclass tăng theo tích | Bridge | Hai trục có thay đổi độc lập thật không? |
| Cấu trúc cây cần xử lý đồng nhất | Composite | Operation có hợp lệ cho cả leaf và group không? |
| Cần ghép logging/retry/cache | Decorator | Thứ tự wrapper có ảnh hưởng semantics không? |
| Subsystem quá phức tạp cho client | Facade | Facade có đang chứa business logic quá nhiều không? |
| Cần authorization/lazy/remote/cache | Proxy | Client có cần biết latency và consistency không? |
| Workflow gồm handler có thể dừng | Chain | Thứ tự và short-circuit đã explicit chưa? |
| Cần queue/audit/undo một ý định | Command | Đây là yêu cầu thực hiện hay fact đã xảy ra? |
| Lifecycle có transition phức tạp | State | Transition có đủ phức tạp hơn enum + table không? |
| Nhiều subscriber phản ứng với fact | Observer | Delivery sync/async, retry và idempotency là gì? |
| Persistence boundary cho write model | Repository | Repository có thêm ngôn ngữ domain hay chỉ bọc ORM? |
| Read query phức tạp | Query Object | Có cần projection/pagination chuyên biệt không? |
| Rule nghiệp vụ cần đặt tên/kết hợp | Specification | Rule có đủ tái sử dụng để đáng tạo abstraction không? |

## Liên kết học thực hành

- [Cheatsheets](cheatsheets/README.md): tra cứu nhanh sau khi đã hiểu bài chính.
- [Interactive Learning](docs/08-interactive/README.md): decision tree và design challenge.
- [Examples](examples/README.md): code trước/sau theo tình huống.
- [Exercises](exercises/README.md): bài tập tăng dần và lời giải tham khảo.
- [Kata](kata/README.md): luyện refactor ngắn.
- [Labs](labs/README.md): starter, solution và acceptance test.
- [Playground](playground/README.md): drill nhỏ và mini-application CLI.
- [Framework Integration](framework-integration/README.md): Laravel và Symfony.
- [Production](production/README.md): failure mode, observability và runbook.
- [Interviews](interviews/README.md): câu hỏi–đáp án theo level.

## Cách tránh học sai

- Không chọn pattern chỉ vì thấy `if/else`; trước hết xác định change axis.
- Không tạo interface chỉ để “dễ mock”; ưu tiên boundary có khả năng thay đổi hoặc side effect.
- Không dùng benchmark micro để quyết định architecture.
- Không coi pattern là mục tiêu; code đơn giản, testable và dễ thay đổi mới là mục tiêu.
- Không copy ví dụ production nếu chưa xác định transaction, concurrency, retry và observability của hệ thống thật.

## Cách dùng tài nguyên thực hành sau V5.5

- **Examples**: đọc `before.php`, dự đoán failure, sau đó mới xem `after.php` và README của đúng use case.
- **Exercises**: mỗi module là đề bài tự chứa; bắt đầu từ “Đề bài gốc”, không cần tìm context ở nơi khác.
- **Framework Integration**: dùng checklist của đúng bài, không áp một checklist chung cho mọi feature.
- **Handbook**: học theo thứ tự Vấn đề → Khái niệm → Mental model → Ví dụ → Bài tập.
- **Production**: bắt đầu từ invariant và source of truth, sau đó mới đọc event, retry, metric và runbook.


## Bổ sung chuyên gia mới

- [Abstraction Retirement](docs/09-expert-practice/16-abstraction-retirement.md)
- [Design Decision Observability](docs/09-expert-practice/17-design-decision-observability.md)
