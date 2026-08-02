# Tech Lead — Governance, mentoring và vận hành

Bộ câu hỏi Tech Lead tập trung decision quality, governance, coaching, architecture evidence, rollout/rollback và tiêu chí xem xét lại một abstraction ở quy mô team.

## 1. Làm sao governance pattern mà không giáo điều?

**Trả lời chi tiết:** Định nghĩa principles và decision records, cung cấp examples/counterexamples, tự động hóa guardrail quan trọng và cho phép exception có evidence. Review problem/forces trước tên pattern.

**Cách ghi điểm:** Không tạo “approved pattern list” tuyệt đối.

**Câu hỏi đào sâu:** Với chủ đề **Làm sao governance pattern mà không giáo điều**, mô tả decision drivers, owner, exception path, revisit signal và cách tránh biến ADR thành thủ tục hình thức. Bổ sung decision drivers, owner, governance guardrail và revisit signal cho toàn team.

## 2. Khi nào yêu cầu ADR?

**Trả lời chi tiết:** Quyết định khó đảo ngược, ảnh hưởng nhiều team/data/contract, có trade-off đáng kể hoặc cần nhớ rationale. Không dùng ADR cho refactor cục bộ dễ đảo.

**Cách ghi điểm:** ADR có revisit trigger và owner.

**Câu hỏi đào sâu:** Với chủ đề **Khi nào yêu cầu ADR**, mô tả decision drivers, owner, exception path, revisit signal và cách tránh biến ADR thành thủ tục hình thức. Bổ sung decision drivers, owner, governance guardrail và revisit signal cho toàn team.

## 3. Đánh giá proposal Repository cho Laravel thế nào?

**Trả lời chi tiết:** Hỏi domain semantics, aggregate, query/reporting, test seam và persistence volatility. Nếu chỉ bọc `Model::find`, chọn Eloquent trực tiếp hoặc Query Object.

**Cách ghi điểm:** Yêu cầu before/after call site.

**Câu hỏi đào sâu:** Với chủ đề **Đánh giá proposal Repository cho Laravel thế nào**, định nghĩa collection semantics, aggregate boundary và transaction expectation; so sánh với Eloquent/query trực tiếp bằng một use case. Bổ sung decision drivers, owner, governance guardrail và revisit signal cho toàn team.

## 4. Mentor Junior học pattern ra sao?

**Trả lời chi tiết:** Bắt đầu code smell và scenario; cho thử baseline; refactor có test; yêu cầu giải thích trade-off và khi không dùng; review vocabulary sau.

**Cách ghi điểm:** Không bắt học thuộc 23 UML.

**Câu hỏi đào sâu:** Với chủ đề **Mentor Junior học pattern ra sao**, đánh giá “Mentor Junior học pattern ra sao” theo decision drivers, alternatives, governance, owner, revisit signal và cách coaching team. Bổ sung decision drivers, owner, governance guardrail và revisit signal cho toàn team.

## 5. Review PR có nhiều abstraction thế nào?

**Trả lời chi tiết:** Trace use case end-to-end, đếm decision được che giấu chứ không đếm lớp, kiểm tra owner/change axis, failure path, test và deletion path.

**Cách ghi điểm:** Thử inline một lớp để xem giá trị.

**Câu hỏi đào sâu:** Với chủ đề **Review PR có nhiều abstraction thế nào**, đánh giá “Review PR có nhiều abstraction thế nào” theo decision drivers, alternatives, governance, owner, revisit signal và cách coaching team. Bổ sung decision drivers, owner, governance guardrail và revisit signal cho toàn team.

## 6. Khi nào tách bounded context thành service?

**Trả lời chi tiết:** Chỉ khi boundary domain đã rõ và có nhu cầu ownership/deploy/scale độc lập. Tách sớm tạo distributed coupling và transaction phức tạp.

**Cách ghi điểm:** Ưu tiên modular monolith để học boundary.

**Câu hỏi đào sâu:** Với chủ đề **Khi nào tách bounded context thành service**, đánh giá “Khi nào tách bounded context thành service” theo decision drivers, alternatives, governance, owner, revisit signal và cách coaching team. Bổ sung decision drivers, owner, governance guardrail và revisit signal cho toàn team.

## 7. Quản lý architecture drift thế nào?

**Trả lời chi tiết:** Fitness functions, dependency visualization, ADR review, exception register và định kỳ xóa rule lỗi thời. Drift quan trọng khi phá invariant/ownership, không phải khác style.

**Cách ghi điểm:** Gắn alert với owner.

**Câu hỏi đào sâu:** Với chủ đề **Quản lý architecture drift thế nào**, đánh giá “Quản lý architecture drift thế nào” theo decision drivers, alternatives, governance, owner, revisit signal và cách coaching team. Bổ sung decision drivers, owner, governance guardrail và revisit signal cho toàn team.

## 8. Thiết kế chương trình training pattern cho team?

**Trả lời chi tiết:** Chia level, mỗi buổi có scenario, live refactor, exercise, rubric và production consequence; capstone yêu cầu evidence và presentation.

**Cách ghi điểm:** Đo khả năng ra quyết định, không đo số pattern nhớ.

**Câu hỏi đào sâu:** Với chủ đề **Thiết kế chương trình training pattern cho team**, đánh giá “Thiết kế chương trình training pattern cho team” theo decision drivers, alternatives, governance, owner, revisit signal và cách coaching team. Bổ sung decision drivers, owner, governance guardrail và revisit signal cho toàn team.

## 9. Đánh giá microservice over-engineering?

**Trả lời chi tiết:** So sánh với modular monolith theo team size, deploy frequency, scale profile, consistency, operability và incident capability.

**Cách ghi điểm:** Yêu cầu total cost of ownership.

**Câu hỏi đào sâu:** Với chủ đề **Đánh giá microservice over-engineering**, chọn một abstraction thật, liệt kê cost of change/trace/test và đề xuất experiment inline/xóa để so sánh evidence. Bổ sung decision drivers, owner, governance guardrail và revisit signal cho toàn team.

## 10. Quyết định build hay dùng framework abstraction?

**Trả lời chi tiết:** Đánh giá semantics, extension need, source stability, operational maturity và exit cost. Dùng framework default nếu phù hợp; wrap boundary khi vendor leakage có risk thật.

**Cách ghi điểm:** Đọc source/framework lifecycle.

**Câu hỏi đào sâu:** Với chủ đề **Quyết định build hay dùng framework abstraction**, đánh giá “Quyết định build hay dùng framework abstraction” theo decision drivers, alternatives, governance, owner, revisit signal và cách coaching team. Bổ sung decision drivers, owner, governance guardrail và revisit signal cho toàn team.

## 11. Làm sao chuẩn hóa event catalog?

**Trả lời chi tiết:** Owner, schema, semantics, version, retention, PII, ordering key, delivery guarantee và consumers. Contract tests và compatibility check trong CI.

**Cách ghi điểm:** Không biến catalog thành tài liệu chết.

**Câu hỏi đào sâu:** Với chủ đề **Làm sao chuẩn hóa event catalog**, phân biệt intent và fact, ownership của retry, versioning payload và cách tránh dùng event như RPC ẩn. Bổ sung decision drivers, owner, governance guardrail và revisit signal cho toàn team.

## 12. Release pattern migration cần approval gì?

**Trả lời chi tiết:** Data/backfill plan, dual-run evidence, rollback tested, dashboards, stop conditions, on-call owner và cleanup issue.

**Cách ghi điểm:** Go/no-go dựa metric.

**Câu hỏi đào sâu:** Với chủ đề **Release pattern migration cần approval gì**, thiết kế expand–migrate–contract hoặc dual-run, xác định shadow diff, cutover gate và rollback trigger. Bổ sung decision drivers, owner, governance guardrail và revisit signal cho toàn team.

## 13. Đánh giá test architecture?

**Trả lời chi tiết:** Pyramid theo risk: domain/unit nhanh, contract cho ports, integration cho adapters, E2E ít; mutation/property tests cho invariant quan trọng.

**Cách ghi điểm:** Theo dõi flakiness và thời gian feedback.

**Câu hỏi đào sâu:** Với chủ đề **Đánh giá test architecture**, đánh giá “Đánh giá test architecture” theo decision drivers, alternatives, governance, owner, revisit signal và cách coaching team. Bổ sung decision drivers, owner, governance guardrail và revisit signal cho toàn team.

## 14. Làm sao tạo văn hóa phản biện thiết kế?

**Trả lời chi tiết:** Review alternative và assumption công khai, blameless postmortem, decision log, rotating facilitator và reward cho việc xóa abstraction không còn giá trị.

**Cách ghi điểm:** Leader thừa nhận uncertainty.

**Câu hỏi đào sâu:** Với chủ đề **Làm sao tạo văn hóa phản biện thiết kế**, đánh giá “Làm sao tạo văn hóa phản biện thiết kế” theo decision drivers, alternatives, governance, owner, revisit signal và cách coaching team. Bổ sung decision drivers, owner, governance guardrail và revisit signal cho toàn team.

## 15. Cách trả lời system design để ghi điểm?

**Trả lời chi tiết:** Bắt đầu requirement/invariant, capacity/failure, ownership; đưa baseline, alternative, trade-off; vẽ data/sequence; nói migration, metric và rollback.

**Cách ghi điểm:** Không nhảy ngay vào Kafka/CQRS.

**Câu hỏi đào sâu:** Với chủ đề **Cách trả lời system design để ghi điểm**, đánh giá “Cách trả lời system design để ghi điểm” theo decision drivers, alternatives, governance, owner, revisit signal và cách coaching team. Bổ sung decision drivers, owner, governance guardrail và revisit signal cho toàn team.