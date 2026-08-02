# OOP Review trong PHP

## Mục tiêu học tập

Sau bài này, người học có thể phân biệt **encapsulation**, **abstraction**, **polymorphism** và **inheritance**; nhận biết khi một class chỉ đang chứa dữ liệu và khi nó thực sự bảo vệ quy tắc nghiệp vụ.

## Vì sao cần ôn OOP trước Design Pattern?

Design Pattern không thay thế OOP. Pattern chỉ có giá trị khi object có trách nhiệm rõ, contract ổn định và invariant được bảo vệ. Nếu class chỉ gồm getter/setter, việc thêm interface hoặc factory thường chỉ làm code dài hơn.

## Bốn khái niệm cốt lõi

### Encapsulation

Encapsulation là đặt dữ liệu và quy tắc thay đổi dữ liệu trong cùng một boundary. `private` chỉ là công cụ; mục tiêu là không cho object đi vào trạng thái không hợp lệ.

### Abstraction

Abstraction giữ lại điều client cần biết và che giấu chi tiết thay đổi. Một abstraction tốt thường có vocabulary theo nghiệp vụ, ví dụ `PaymentGateway`, không phải `CommonManager`.

### Polymorphism

Polymorphism cho phép nhiều implementation tuân theo cùng contract. Giá trị của nó nằm ở khả năng thay thế mà không phá precondition, postcondition và exception contract.

### Inheritance

Inheritance phù hợp khi subtype thật sự là một biến thể của base type và có thể thay thế hoàn toàn. Nếu chỉ muốn tái sử dụng vài hành vi, composition thường an toàn hơn.

## Ví dụ: bảo vệ invariant

```php
<?php

declare(strict_types=1);

final class BankAccount
{
    public function __construct(private int $balanceInCents)
    {
        if ($balanceInCents < 0) {
            throw new InvalidArgumentException('Số dư ban đầu không được âm.');
        }
    }

    public function withdraw(int $amountInCents): void
    {
        if ($amountInCents <= 0) {
            throw new InvalidArgumentException('Số tiền rút phải lớn hơn 0.');
        }
        if ($amountInCents > $this->balanceInCents) {
            throw new DomainException('Số dư không đủ.');
        }
        $this->balanceInCents -= $amountInCents;
    }

    public function balanceInCents(): int
    {
        return $this->balanceInCents;
    }
}
```

Điểm quan trọng không phải thuộc tính `private`, mà là không có đường đi hợp lệ nào khiến số dư âm.

## Câu hỏi phân tích

1. Nếu thêm public setter cho số dư, invariant nào có thể bị phá?
2. `BankAccount` đang che giấu chi tiết nào và public API đang biểu đạt nghiệp vụ gì?
3. Nếu `SavingsAccount` cấm rút quá ba lần mỗi tháng, kế thừa có còn đảm bảo substitutability không?
4. Đâu là dấu hiệu cho thấy một class đang trở thành “data bag”?

## Bài tập

Refactor một class `Order` đang có public properties `status`, `total`, `cancelledAt` thành object bảo vệ được quy tắc: đơn đã giao không thể hủy và tổng tiền không thể âm.

### Gợi ý cách làm

1. Viết test cho hai trạng thái hợp lệ và hai transition không hợp lệ trước khi sửa code.
2. Đưa property về `private`; chỉ expose method thể hiện hành động như `cancel()` hoặc `markAsShipped()`.
3. Dùng exception nghiệp vụ thay vì âm thầm bỏ qua transition sai.
4. Kiểm tra lại xem client còn có thể sửa trạng thái trực tiếp hay không.

## Tự kiểm tra

- Bạn có thể giải thích invariant của object bằng một câu không?
- Public method có dùng ngôn ngữ nghiệp vụ không?
- Có setter nào cho phép bỏ qua validation không?
