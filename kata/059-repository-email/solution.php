<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class EmailRecord
{
    public function __construct(public string $id, public string $status)
    {
    }
}
interface EmailRepository
{
    public function save(EmailRecord $record): void;
    public function byId(string $id): ?EmailRecord;
}
final class InMemoryEmailRepository implements EmailRepository
{
    private array $items = [];
    public function save(EmailRecord $r): void
    {
        $this->items[$r->id] = $r;
    }
    public function byId(string $id): ?EmailRecord
    {
        return $this->items[$id] ?? null;
    }
}
$repo = new InMemoryEmailRepository();
$repo->save(new EmailRecord('welcome@example.com', 'active'));
expect($repo->byId('welcome@example.com')?->status === 'active', 'repository round trip');
expect($repo->byId('missing') === null, 'missing');
echo 'PASS kata 59' . PHP_EOL;
