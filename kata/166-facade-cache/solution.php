<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class CacheValidator
{
    public function valid(string $id): bool
    {
        return $id !== '';
    }
}
final class CacheStore
{
    public function save(string $id): string
    {
        return 'saved:' . $id;
    }
}
final class CacheNotifier
{
    public function notify(string $id): string
    {
        return 'notified:' . $id;
    }
}
final class CacheFacade
{
    public function __construct(private CacheValidator $validator, private CacheStore $store, private CacheNotifier $notifier)
    {
    }
    public function process(string $id): array
    {
        if (!$this->validator->valid($id))
            throw new InvalidArgumentException('id');
        return [$this->store->save($id), $this->notifier->notify($id)];
    }
}
$result = (new CacheFacade(new CacheValidator(), new CacheStore(), new CacheNotifier()))->process('customer:42');
expect($result === ['saved:customer:42', 'notified:customer:42'], 'facade workflow');
echo 'PASS kata 166' . PHP_EOL;
