<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class ShippingValidator
{
    public function valid(string $id): bool
    {
        return $id !== '';
    }
}
final class ShippingStore
{
    public function save(string $id): string
    {
        return 'saved:' . $id;
    }
}
final class ShippingNotifier
{
    public function notify(string $id): string
    {
        return 'notified:' . $id;
    }
}
final class ShippingFacade
{
    public function __construct(private ShippingValidator $validator, private ShippingStore $store, private ShippingNotifier $notifier)
    {
    }
    public function process(string $id): array
    {
        if (!$this->validator->valid($id))
            throw new InvalidArgumentException('id');
        return [$this->store->save($id), $this->notifier->notify($id)];
    }
}
$result = (new ShippingFacade(new ShippingValidator(), new ShippingStore(), new ShippingNotifier()))->process('HCM-HN');
expect($result === ['saved:HCM-HN', 'notified:HCM-HN'], 'facade workflow');
echo 'PASS kata 10' . PHP_EOL;
