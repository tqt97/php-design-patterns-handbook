# Testing Patterns — Quick Reference

Chọn loại test theo boundary thay vì chạy theo kim tự tháp một cách máy móc.

## Bảng tra nhanh

| Chủ đề | Hướng dẫn |
| --- | --- |
| **Strategy/Specification** | Contract test cho mọi implementation; unit test từng rule và edge case. |
| **Adapter** | Contract test với fixture vendor; test mapping request, response, timeout và error taxonomy. |
| **Decorator** | Test behavior bổ sung và thứ tự wrapper; xác nhận wrapper giữ nguyên contract. |
| **Observer/Event** | Test publisher phát đúng event; subscriber idempotent; integration test delivery semantics. |
| **Repository** | Contract test dùng chung cho in-memory và database implementation; kiểm tra identity và missing entity. |
| **State** | Bảng transition hợp lệ/bất hợp lệ; test side effect chỉ chạy sau transition thành công. |

## Quy trình áp dụng

1. Xác định quyết định liên quan đến **Testing Patterns — Quick Reference** và viết một ví dụ cụ thể đang gây khó khăn.
2. Dùng mục **Strategy/Specification** để kiểm tra trường hợp chính; đối chiếu **Adapter** cho boundary hoặc phương án thay thế.
3. Chuyển lựa chọn thành một test, metric hoặc review question có thể xác minh.
4. Ghi rõ giới hạn của checklist `Testing Patterns — Quick Reference` để tránh áp dụng như quy tắc tuyệt đối.

## Lưu ý thực chiến

- Không mock value object hoặc entity đơn giản.
- Mock boundary có latency/failure, không mock implementation detail.
- Mỗi test nên mô tả một behavior hoặc invariant.

## Câu hỏi review

- Trong bối cảnh hiện tại, mục nào của **Testing Patterns — Quick Reference** ảnh hưởng trực tiếp đến invariant hoặc user outcome?
- Failure nào trở nên dễ chẩn đoán hơn khi áp dụng hướng dẫn **Strategy/Specification**?
- Có thể bỏ bớt abstraction hoặc bước vận hành nào mà vẫn giữ đúng contract không?

## Ma trận failure test theo pattern

| Pattern | Failure quan trọng | Test nên ưu tiên |
| --- | --- | --- |
| Adapter | vendor timeout, schema drift, error code lạ | contract test và error translation |
| Observer | duplicate, ordering, listener failure | idempotency và isolation |
| State | illegal transition, stale version | transition table và optimistic locking |
| Repository | missing aggregate, version conflict | contract suite cho mọi implementation |
| Unit of Work | partial commit, rollback failure | integration test ở transaction boundary |
| Outbox | publish trùng, backlog, poison message | replay-safe consumer và reconciliation |

Một test suite tốt không chỉ chứng minh happy path. Nó phải mô tả semantics khi dependency trả kết quả mơ hồ, khi cùng command được replay và khi transaction bị ngắt giữa chừng.
