<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class CheckoutValidator
{
    public function valid(string $id): bool
    {
        return $id !== '';
    }
}
final class CheckoutStore
{
    public function save(string $id): string
    {
        return 'saved:' . $id;
    }
}
final class CheckoutNotifier
{
    public function notify(string $id): string
    {
        return 'notified:' . $id;
    }
}
final class CheckoutFacade
{
    public function __construct(private CheckoutValidator $validator, private CheckoutStore $store, private CheckoutNotifier $notifier)
    {
    }
    public function process(string $id): array
    {
        if (!$this->validator->valid($id))
            throw new InvalidArgumentException('id');
        return [$this->store->save($id), $this->notifier->notify($id)];
    }
}
$result = (new CheckoutFacade(new CheckoutValidator(), new CheckoutStore(), new CheckoutNotifier()))->process('checkout-101');
expect($result === ['saved:checkout-101', 'notified:checkout-101'], 'facade workflow');
echo 'PASS kata 154' . PHP_EOL;
