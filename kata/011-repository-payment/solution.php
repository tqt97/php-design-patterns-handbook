<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class PaymentRecord
{
    public function __construct(public string $id, public string $status)
    {
    }
}
interface PaymentRepository
{
    public function save(PaymentRecord $record): void;
    public function byId(string $id): ?PaymentRecord;
}
final class InMemoryPaymentRepository implements PaymentRepository
{
    private array $items = [];
    public function save(PaymentRecord $r): void
    {
        $this->items[$r->id] = $r;
    }
    public function byId(string $id): ?PaymentRecord
    {
        return $this->items[$id] ?? null;
    }
}
$repo = new InMemoryPaymentRepository();
$repo->save(new PaymentRecord('pay_1001', 'active'));
expect($repo->byId('pay_1001')?->status === 'active', 'repository round trip');
expect($repo->byId('missing') === null, 'missing');
echo 'PASS kata 11' . PHP_EOL;
