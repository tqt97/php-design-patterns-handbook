<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class UserRecord
{
    public function __construct(public string $id, public string $status)
    {
    }
}
interface UserRepository
{
    public function save(UserRecord $record): void;
    public function byId(string $id): ?UserRecord;
}
final class InMemoryUserRepository implements UserRepository
{
    private array $items = [];
    public function save(UserRecord $r): void
    {
        $this->items[$r->id] = $r;
    }
    public function byId(string $id): ?UserRecord
    {
        return $this->items[$id] ?? null;
    }
}
$repo = new InMemoryUserRepository();
$repo->save(new UserRecord('user-42', 'active'));
expect($repo->byId('user-42')?->status === 'active', 'repository round trip');
expect($repo->byId('missing') === null, 'missing');
echo 'PASS kata 167' . PHP_EOL;
