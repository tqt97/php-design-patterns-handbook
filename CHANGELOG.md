# Changelog tổng hợp

Tài liệu này ghi lại toàn bộ nhóm cải tiến của repository theo capability, không chia theo version.

## Repository foundation

- Chuẩn hóa cấu trúc tài liệu, Composer, PSR-4, PHPUnit, PHPStan, PHP CS Fixer và GitHub Actions.
- Thêm template cho pattern article, lab và Architecture Decision Record.
- Bổ sung README, Overview, Manifest, Release Checklist và Directory Quality Matrix.

## Nội dung Design Pattern

- Hoàn thiện 23 GoF Pattern, 7 Enterprise Pattern và Laravel/Symfony Integration.
- Mỗi bài có problem, intent, before/after, UML/Mermaid, test, trade-off, exercise và interview answer.
- Loại bỏ participant generic và nội dung chỉ thay tiêu đề.

## Thực hành

- Xây dựng examples, 52 exercise modules, 204 kata, 19 lab và 120 playground.
- Bổ sung lời giải, acceptance criteria, failure path, expected output và design review questions.
- Chuyên biệt hóa toàn bộ diagram, evidence và workshop flow theo chủ đề.

## Production engineering

- Xây dựng Payment, Notification, Booking, Inventory, CRM và Order Management platforms.
- Bổ sung invariant, source of truth, idempotency, concurrency, reconciliation, telemetry và runbook.
- Mở rộng Fulfillment, Overbooking và các capability có ambiguous external outcome.

## Handbook, interviews và training

- Mở rộng Software Design, Refactoring, Clean Architecture, DDD, Microservices và Engineering Playbook.
- Xây dựng interview bank theo level và live design scenarios.
- Hoàn thiện 15 training lesson với slides, speaker notes, exercise, quiz và demo PHP.

## Source code và testing

- Bổ sung implementation cho GoF, Enterprise Patterns, Idempotency, Outbox, Retry Policy, Command Bus và Value Objects.
- Thêm Circuit Breaker có state machine, PHPUnit test và source smoke scenario.
- Chuẩn hóa source map, strict types và evidence linking.

## Enterprise quality automation

- Thêm audit cho link, heading, semantic, README, repetition, folder uniqueness, diagrams, exercises, production, playground, source map và release quality.
- Thêm `enterprise-release-v2-audit` để kiểm tra độ sâu theo thư mục, Mermaid fence, participant generic và source–test evidence.
- Duy trì Changelog dạng cumulative và ghi rõ giới hạn môi trường kiểm thử.

## Editorial hardening

- Viết lại các phần quan sát, câu hỏi review, test focus và trade-off để mỗi bài phản ánh đúng pattern/domain.
- Kiểm tra trùng lặp ở mức body, paragraph, section, diagram và similarity sau khi bỏ code/title.
- Chuẩn hóa câu chữ tiếng Việt, thuật ngữ Anh–Việt và cách trình bày bảng, code block, diagram.
- Mở rộng các bài ngắn bằng mental model, failure rehearsal, evidence và Definition of Done thay vì thêm prose chung.

## Diagram và learning experience

- Thay các sơ đồ generic bằng class, sequence, state hoặc flow diagram có participant thật.
- Chuyên biệt hóa Mental Model của Handbook, target diagram của Kata, production analysis của Framework Integration và design model của Exercises.
- Bổ sung decision matrix, comparison map và flow thuyết trình cho Training/Interactive Learning.
- Kiểm tra Mermaid fence, diagram uniqueness và sự tương thích giữa sơ đồ với tiêu đề/bối cảnh.

## Framework và architecture

- Làm rõ Laravel container lifecycle, Service Provider, Events/Jobs, Pipeline, Middleware, Repository/Query Object và Transactional Outbox.
- Làm rõ Symfony compiled container, Messenger delivery semantics, Doctrine flush boundary, Event Dispatcher và HttpClient Adapter.
- Bổ sung architecture fitness, context map, aggregate boundary, saga/process manager và migration playbook.
- Phân biệt framework wiring với application/domain policy để tránh framework leakage.

## Operability và resilience

- Mô hình hóa transient, permanent và ambiguous failure; bổ sung retry budget, reconciliation và dead-letter handling.
- Thêm Idempotency Store, Outbox, Retry Policy và Circuit Breaker với source/test evidence.
- Mở rộng production modules bằng dashboard metric, oldest pending age, correlation id, runbook và verification query.
- Nhấn mạnh rollback dữ liệu, progressive rollout, feature flag và post-release verification thay vì chỉ rollback code.

## Teaching, mentoring và interview

- Xây dựng learning path theo Junior, Middle, Senior và Tech Lead.
- Mỗi lesson có mục tiêu, flow, demo, failure injection, exercise, rubric và quiz có đáp án.
- Interview answers được mở rộng bằng Context, Forces, Options, Decision, Trade-off, Evidence và Revisit condition.
- Live design scenarios đánh giá invariant, failure, operability và khả năng giải thích, không chấm số pattern được nhắc tới.

## Release governance

- Bổ sung Directory Quality Matrix, Release Checklist, Review Report, Audit Cycles và Source Enterprise Review.
- Changelog được chuyển sang lịch sử cumulative, không dùng heading version.
- Manifest được đồng bộ bằng số liệu thực tế từ filesystem.
- Ghi rõ PHPUnit, PHPStan và PHP CS Fixer chỉ được xác nhận khi Composer dependencies hiện diện; smoke tests và syntax checks không được mô tả thay thế các tool đó.

## Enterprise learning system và capacity isolation

- Viết lại README theo luồng problem → baseline → design → test → operability → revisit.
- Thêm `ENTERPRISE_LEARNING_REVIEW.md` với artifact, evidence và rubric theo từng thư mục.
- Bổ sung capstone riêng cho năm cấp Training và failure rehearsal cho năm case study.
- Mở rộng các bài so sánh pattern bằng production scenario, dấu hiệu chọn sai và test strategy.
- Bổ sung Bulkhead source, exception, PHPUnit test, smoke scenario và Expert Practice article.
- Thêm `artifact-alignment-audit` để kiểm tra fenced blocks, Mermaid type/flow và participant generic.
- Thêm `folder-enterprise-depth-audit` với ngưỡng prose theo từng loại tài liệu.
- Mở rộng Testing, Pattern Composition, API Design, Interview và Lab guidance bằng nội dung thực tiễn riêng.

## Advanced testing, migration và operational evidence

- Bổ sung property-based testing workbook có seed cho Money, discount, stock và booking overlap.
- Bổ sung cấu hình và hướng dẫn mutation testing bằng Infection.
- Thêm Distributed Bulkhead model với lease TTL, permit store, unit test và smoke scenario.
- Thêm deterministic Failure Injector và failure-injection lab.
- Thêm Dual-run Comparator cho migration rehearsal và shadow comparison.
- Thêm architecture fitness script và CI-ready Composer command.
- Thêm framework source-tour protocol cùng Laravel/Symfony tour.
- Thêm Incident Packet template, payment incident sample và Design Evidence Graph.
- Thêm quality gate `expert-practice-v2-audit` để bảo vệ độ sâu và artifact chuyên gia.

## Enterprise editorial and simulation hardening

- Bổ sung interaction diagram cho các example còn thiếu sơ đồ.
- Mở rộng Laravel pattern bằng production case, test matrix, telemetry và runbook expectation riêng.
- Nâng 19 Lab bằng lời giải định hướng, expected result, failure test và operational signals.
- Mở rộng 15 speaker notes thành full flow 90 phút với evidence và debrief riêng.
- Bổ sung enterprise operating model cho sáu Production Platform.
- Thêm `DeduplicationWindow` cùng PHPUnit test và source smoke scenario.
- Bổ sung bảng chọn lộ trình thực tiễn trong README và backlog nâng cấp chuyên gia.

## Alignment, Rate Limiting và Release Revalidation

- Viết lại quy chuẩn sơ đồ và Foundation navigation để làm rõ artifact, failure branch và Definition of Done.
- Sửa Order State production design theo đúng aggregate ownership, optimistic concurrency, transaction và outbox semantics.
- Bổ sung Factory Method UML đúng participant và phân biệt với Simple Factory.
- Mở rộng Lab solution và flagship playground bằng failure walkthrough, runtime evidence, metric và recovery.
- Thêm Rate Limiter source/test/smoke, tài liệu admission control và hướng distributed adapter.
- Bổ sung test lifecycle cho State và quality gate đối chiếu topic–code–test.
- Chạy lại toàn bộ content/source/executable audit và cập nhật tài liệu phát hành theo số liệu thực tế.

## Backpressure, operability matrix và simulation coverage

- Chuẩn hóa số thứ tự Expert Practice để loại bỏ hai cặp file trùng prefix `20` và `21`.
- Thêm `BoundedWorkQueue` cùng `EnqueueDecision`, PHPUnit test và source smoke scenario.
- Bổ sung chuyên đề Backpressure và Bounded Work Queue với failure matrix, observability, runbook và bài tập fairness theo tenant.
- Thêm `ENTERPRISE_OPERABILITY_MATRIX.md` nối pattern với invariant, failure, test, metric và recovery.
- Thêm `enterprise-simulation-coverage-audit` để bảo đảm các capability resilience/migration có source, test, tài liệu và smoke evidence.
