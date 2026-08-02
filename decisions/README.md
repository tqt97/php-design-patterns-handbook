# Architecture Decision Records

Kho ADR mô tả những quyết định kiến trúc có context, alternatives, consequences và cách kiểm chứng.

## Cách sử dụng

- Đọc để học cách lập luận, không copy quyết định sang hệ thống khác.
- Khi viết ADR mới, ghi rõ forces, lựa chọn bị loại và điều kiện xem xét lại.
- Liên kết ADR với test, metric hoặc migration plan khi có thể.

## Danh mục

- [ADR-011: Dùng Query Object cho màn hình báo cáo](011-use-query-object-for-reporting.md)
- [ADR-012: Không đưa Eloquent model vào domain core](012-keep-eloquent-out-of-domain.md)
- [ADR-013: Dùng Transactional Outbox](013-use-outbox-for-integration-events.md)
- [ADR-014: Bắt buộc idempotency cho payment command](014-idempotency-for-payment-commands.md)
- [ADR-015: Không dùng Generic Base Repository](015-no-generic-base-repository.md)
- [ADR-016: Dùng Value Object cho Money](016-use-value-object-for-money.md)
- [ADR-017: Domain event chỉ phát sau thay đổi hợp lệ](017-domain-events-after-state-change.md)
- [ADR-018: Dùng Composition Root](018-prefer-composition-root.md)
- [ADR-019: Optimistic locking cho booking](019-use-optimistic-locking-for-booking.md)
- [ADR-020: Tách Domain Event và Integration Event](020-separate-domain-and-integration-events.md)
- [ADR-021: Dùng Policy Object cho discount](021-use-policy-object-for-discounts.md)
- [ADR-022: Một Command Handler cho mỗi use case ghi](022-command-handler-per-use-case.md)
- [ADR-023: Không dùng Event Sourcing mặc định](023-no-event-sourcing-by-default.md)
- [ADR-024: Dùng Saga cho workflow xuyên service](024-use-saga-for-cross-service-workflow.md)
- [ADR-025: Cache-aside cho product catalog](025-cache-aside-for-product-catalog.md)
- [ADR-026: Anti-Corruption Layer cho CRM cũ](026-use-anti-corruption-layer-for-legacy-crm.md)
- [ADR-027: Chuẩn hóa lỗi domain có mã](027-structured-domain-errors.md)
- [ADR-028: Không dùng Service Locator trong domain](028-avoid-service-locator.md)
- [ADR-029: Contract test cho adapter](029-contract-tests-for-adapters.md)
- [ADR-030: Cursor pagination cho activity feed](030-use-cursor-pagination.md)
- [ADR-031: Chỉ soft delete khi có recovery/audit requirement](031-soft-delete-only-for-recovery.md)
- [ADR-032: Version Integration Event](032-version-integration-events.md)
- [ADR-033: Feature flag cho refactor rủi ro](033-feature-flags-for-risky-refactors.md)
- [ADR-034: Hexagonal boundary cho payment gateway](034-hexagonal-boundaries-for-payment.md)
- [ADR-035: Lưu thời gian UTC, giữ timezone nghiệp vụ](035-store-times-in-utc.md)

## Quality checklist

- Nội dung phải đúng với mục tiêu của thư mục và có liên kết điều hướng rõ.
- Ví dụ hoặc lời giải phải nêu invariant, failure path và trade-off.
- Không dùng tài liệu tra cứu nhanh thay cho việc hiểu context của pattern.  
> **Ngữ cảnh áp dụng:** Đối với quyết định **Architecture Decision Records**, bằng chứng chấp nhận phải đến từ metric, test hoặc incident data liên quan trực tiếp đến assumption của ADR.
