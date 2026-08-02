# Cheatsheets

Bộ tài liệu tra cứu nhanh dành cho lúc thiết kế, refactor và code review. Mỗi cheatsheet tóm tắt quyết định; hãy quay lại bài đầy đủ khi cần hiểu reasoning.

## Cách sử dụng

- Bắt đầu từ vấn đề hoặc code smell, không từ tên pattern.
- Dùng checklist trong Pull Request hoặc design session.
- Đối chiếu với ADR khi quyết định ảnh hưởng nhiều module.

## Danh mục

- [Chọn pattern theo trục thay đổi](09-pattern-selection-by-change-axis.md)
- [Từ code smell đến hướng refactor](10-code-smell-to-refactoring-map.md)
- [Kiểm thử Design Pattern](11-testing-patterns-quick-reference.md)
- [Ranh giới transaction](12-transaction-boundary-cheatsheet.md)
- [Idempotency thực chiến](13-idempotency-cheatsheet.md)
- [Quyết định dùng event](14-event-driven-decision-guide.md)
- [Bản đồ DDD Tactical Patterns](15-ddd-tactical-patterns-map.md)
- [Boundary trong Clean Architecture](16-clean-architecture-boundary-map.md)
- [Review abstraction](17-reviewing-abstractions.md)
- [Mô hình hóa lỗi](18-error-modeling-cheatsheet.md)
- [Caching patterns](19-caching-patterns.md)
- [Checklist review API](20-api-design-review.md)
- [Kiểm soát đồng thời](21-concurrency-control.md)
- [Observability](22-observability-cheatsheet.md)
- [Security design review](23-security-design-review.md)
- [Kết hợp pattern có chủ đích](24-pattern-composition-guide.md)
- [Safety net khi refactor legacy](25-legacy-refactoring-safety-net.md)
- [Đặt tên trong thiết kế](26-naming-guide.md)
- [Anti-patterns](anti-patterns.md)
- [Từ code smell đến pattern ứng viên](code-smell-to-pattern.md)
- [GoF Overview](gof-overview.md)
- [Bản đồ pattern trong Laravel](laravel-pattern-map.md)
- [So sánh các pattern dễ nhầm](pattern-comparison.md)
- [Pattern Selection Guide](pattern-selection-guide.md)
- [Quy trình refactor sang Design Pattern](refactoring-workflow.md)
- [Cheatsheet kiểm thử Design Pattern](testing-patterns.md)

## Quality checklist

- Nội dung phải đúng với mục tiêu của thư mục và có liên kết điều hướng rõ.
- Ví dụ hoặc lời giải phải nêu invariant, failure path và trade-off.
- Không dùng tài liệu tra cứu nhanh thay cho việc hiểu context của pattern.  
> **Ngữ cảnh áp dụng:** Trong **Cheatsheets**, diễn giải checklist theo boundary và vocabulary của chính chủ đề này thay vì áp dụng máy móc.
