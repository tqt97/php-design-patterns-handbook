<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class InventoryValidator
{
    public function valid(string $id): bool
    {
        return $id !== '';
    }
}
final class InventoryStore
{
    public function save(string $id): string
    {
        return 'saved:' . $id;
    }
}
final class InventoryNotifier
{
    public function notify(string $id): string
    {
        return 'notified:' . $id;
    }
}
final class InventoryFacade
{
    public function __construct(private InventoryValidator $validator, private InventoryStore $store, private InventoryNotifier $notifier)
    {
    }
    public function process(string $id): array
    {
        if (!$this->validator->valid($id))
            throw new InvalidArgumentException('id');
        return [$this->store->save($id), $this->notifier->notify($id)];
    }
}
$result = (new InventoryFacade(new InventoryValidator(), new InventoryStore(), new InventoryNotifier()))->process('SKU-PHP-01');
expect($result === ['saved:SKU-PHP-01', 'notified:SKU-PHP-01'], 'facade workflow');
echo 'PASS kata 22' . PHP_EOL;
