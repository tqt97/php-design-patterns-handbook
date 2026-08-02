<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class BookingValidator
{
    public function valid(string $id): bool
    {
        return $id !== '';
    }
}
final class BookingStore
{
    public function save(string $id): string
    {
        return 'saved:' . $id;
    }
}
final class BookingNotifier
{
    public function notify(string $id): string
    {
        return 'notified:' . $id;
    }
}
final class BookingFacade
{
    public function __construct(private BookingValidator $validator, private BookingStore $store, private BookingNotifier $notifier)
    {
    }
    public function process(string $id): array
    {
        if (!$this->validator->valid($id))
            throw new InvalidArgumentException('id');
        return [$this->store->save($id), $this->notifier->notify($id)];
    }
}
$result = (new BookingFacade(new BookingValidator(), new BookingStore(), new BookingNotifier()))->process('room-12');
expect($result === ['saved:room-12', 'notified:room-12'], 'facade workflow');
echo 'PASS kata 46' . PHP_EOL;
