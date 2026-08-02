<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class AuditValidator
{
    public function valid(string $id): bool
    {
        return $id !== '';
    }
}
final class AuditStore
{
    public function save(string $id): string
    {
        return 'saved:' . $id;
    }
}
final class AuditNotifier
{
    public function notify(string $id): string
    {
        return 'notified:' . $id;
    }
}
final class AuditFacade
{
    public function __construct(private AuditValidator $validator, private AuditStore $store, private AuditNotifier $notifier)
    {
    }
    public function process(string $id): array
    {
        if (!$this->validator->valid($id))
            throw new InvalidArgumentException('id');
        return [$this->store->save($id), $this->notifier->notify($id)];
    }
}
$result = (new AuditFacade(new AuditValidator(), new AuditStore(), new AuditNotifier()))->process('user.updated');
expect($result === ['saved:user.updated', 'notified:user.updated'], 'facade workflow');
echo 'PASS kata 142' . PHP_EOL;
