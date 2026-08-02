<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class OrderValidator
{
    public function valid(string $id): bool
    {
        return $id !== '';
    }
}
final class OrderStore
{
    public function save(string $id): string
    {
        return 'saved:' . $id;
    }
}
final class OrderNotifier
{
    public function notify(string $id): string
    {
        return 'notified:' . $id;
    }
}
final class OrderFacade
{
    public function __construct(private OrderValidator $validator, private OrderStore $store, private OrderNotifier $notifier)
    {
    }
    public function process(string $id): array
    {
        if (!$this->validator->valid($id))
            throw new InvalidArgumentException('id');
        return [$this->store->save($id), $this->notifier->notify($id)];
    }
}
$result = (new OrderFacade(new OrderValidator(), new OrderStore(), new OrderNotifier()))->process('ORD-1001');
expect($result === ['saved:ORD-1001', 'notified:ORD-1001'], 'facade workflow');
echo 'PASS kata 106' . PHP_EOL;
