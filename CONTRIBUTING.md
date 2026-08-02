# Đóng góp cho PHP Design Patterns Handbook

Cảm ơn bạn đã dành thời gian cải thiện repository. Mọi đóng góp nên giúp người đọc hiểu rõ hơn **vấn đề, quyết định và trade-off**, thay vì chỉ tăng số lượng pattern hoặc class.

## Loại đóng góp được khuyến khích

- Sửa lỗi kỹ thuật hoặc lỗi diễn giải.
- Bổ sung code before/after và test.
- Thêm tình huống thực tế từ dự án PHP, Laravel hoặc Symfony.
- Cải thiện diagram, learning path, kata và lab.
- Bổ sung failure mode, observability hoặc production checklist.
- Sửa link hỏng, nội dung trùng hoặc bài viết lệch chủ đề.

## Quy trình

1. Fork repository và tạo branch có tên rõ ràng.
2. Viết hoặc cập nhật test trước khi thay đổi behavior.
3. Chạy toàn bộ quality gate.
4. Tạo Pull Request mô tả context, thay đổi và cách kiểm chứng.

```bash
composer install
composer quality
composer benchmarks
```

## Quy chuẩn nội dung

Mỗi bài pattern nên có:

- Vấn đề và code smell cụ thể.
- Ví dụ before/after có cùng hành vi.
- Khi nên dùng và khi không nên dùng.
- Trade-off, không chỉ ưu điểm.
- Test hoặc tiêu chí kiểm chứng.
- Link tương đối hợp lệ đến bài liên quan.

Không chấp nhận:

- Nội dung chỉ thay tiêu đề hoặc tên domain.
- Code giả không chạy được khi bài khẳng định là executable.
- Benchmark không đo implementation đang được mô tả.
- Interface/factory/repository không có lý do tồn tại.
- Nội dung sao chép không ghi nguồn hoặc vi phạm bản quyền.

## Pull Request checklist

- [ ] `composer quality` chạy thành công.
- [ ] Link Markdown nội bộ không bị hỏng.
- [ ] Nội dung phù hợp với tên file và heading chính.
- [ ] Không có đoạn boilerplate lặp vô nghĩa.
- [ ] Code dùng `strict_types` và style thống nhất.
- [ ] Có mô tả trade-off và giới hạn của giải pháp.

## Giao tiếp

Hãy review trên tinh thần tôn trọng, tập trung vào code và quyết định kỹ thuật. Khi có nhiều giải pháp hợp lệ, ưu tiên giải thích constraint và hậu quả thay vì khẳng định một cấu trúc class duy nhất là “đúng”.
