<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class DiscountValidator
{
    public function valid(string $id): bool
    {
        return $id !== '';
    }
}
final class DiscountStore
{
    public function save(string $id): string
    {
        return 'saved:' . $id;
    }
}
final class DiscountNotifier
{
    public function notify(string $id): string
    {
        return 'notified:' . $id;
    }
}
final class DiscountFacade
{
    public function __construct(private DiscountValidator $validator, private DiscountStore $store, private DiscountNotifier $notifier)
    {
    }
    public function process(string $id): array
    {
        if (!$this->validator->valid($id))
            throw new InvalidArgumentException('id');
        return [$this->store->save($id), $this->notifier->notify($id)];
    }
}
$result = (new DiscountFacade(new DiscountValidator(), new DiscountStore(), new DiscountNotifier()))->process('VIP20');
expect($result === ['saved:VIP20', 'notified:VIP20'], 'facade workflow');
echo 'PASS kata 34' . PHP_EOL;
