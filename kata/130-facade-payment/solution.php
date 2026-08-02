<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class PaymentValidator
{
    public function valid(string $id): bool
    {
        return $id !== '';
    }
}
final class PaymentStore
{
    public function save(string $id): string
    {
        return 'saved:' . $id;
    }
}
final class PaymentNotifier
{
    public function notify(string $id): string
    {
        return 'notified:' . $id;
    }
}
final class PaymentFacade
{
    public function __construct(private PaymentValidator $validator, private PaymentStore $store, private PaymentNotifier $notifier)
    {
    }
    public function process(string $id): array
    {
        if (!$this->validator->valid($id))
            throw new InvalidArgumentException('id');
        return [$this->store->save($id), $this->notifier->notify($id)];
    }
}
$result = (new PaymentFacade(new PaymentValidator(), new PaymentStore(), new PaymentNotifier()))->process('pay_1001');
expect($result === ['saved:pay_1001', 'notified:pay_1001'], 'facade workflow');
echo 'PASS kata 130' . PHP_EOL;
