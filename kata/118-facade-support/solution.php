<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class SupportValidator
{
    public function valid(string $id): bool
    {
        return $id !== '';
    }
}
final class SupportStore
{
    public function save(string $id): string
    {
        return 'saved:' . $id;
    }
}
final class SupportNotifier
{
    public function notify(string $id): string
    {
        return 'notified:' . $id;
    }
}
final class SupportFacade
{
    public function __construct(private SupportValidator $validator, private SupportStore $store, private SupportNotifier $notifier)
    {
    }
    public function process(string $id): array
    {
        if (!$this->validator->valid($id))
            throw new InvalidArgumentException('id');
        return [$this->store->save($id), $this->notifier->notify($id)];
    }
}
$result = (new SupportFacade(new SupportValidator(), new SupportStore(), new SupportNotifier()))->process('TICKET-88');
expect($result === ['saved:TICKET-88', 'notified:TICKET-88'], 'facade workflow');
echo 'PASS kata 118' . PHP_EOL;
