# Architecture Fitness Lab

Lab biến dependency direction và source evidence thành kiểm tra CI.

```bash
php scripts/audit-architecture-fitness.php
```

Mở rộng bài tập:

1. Thêm rule cấm Controller import concrete repository.
2. Thêm allowlist có expiry date.
3. Xuất lỗi dạng GitHub annotation.
4. Viết architecture test cho event schema compatibility.

Fitness function phải bảo vệ rủi ro, không chỉ style preference.
