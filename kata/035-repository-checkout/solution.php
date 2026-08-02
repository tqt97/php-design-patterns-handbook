<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class CheckoutRecord
{
    public function __construct(public string $id, public string $status)
    {
    }
}
interface CheckoutRepository
{
    public function save(CheckoutRecord $record): void;
    public function byId(string $id): ?CheckoutRecord;
}
final class InMemoryCheckoutRepository implements CheckoutRepository
{
    private array $items = [];
    public function save(CheckoutRecord $r): void
    {
        $this->items[$r->id] = $r;
    }
    public function byId(string $id): ?CheckoutRecord
    {
        return $this->items[$id] ?? null;
    }
}
$repo = new InMemoryCheckoutRepository();
$repo->save(new CheckoutRecord('checkout-101', 'active'));
expect($repo->byId('checkout-101')?->status === 'active', 'repository round trip');
expect($repo->byId('missing') === null, 'missing');
echo 'PASS kata 35' . PHP_EOL;
