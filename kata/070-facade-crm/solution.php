<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class CrmValidator
{
    public function valid(string $id): bool
    {
        return $id !== '';
    }
}
final class CrmStore
{
    public function save(string $id): string
    {
        return 'saved:' . $id;
    }
}
final class CrmNotifier
{
    public function notify(string $id): string
    {
        return 'notified:' . $id;
    }
}
final class CrmFacade
{
    public function __construct(private CrmValidator $validator, private CrmStore $store, private CrmNotifier $notifier)
    {
    }
    public function process(string $id): array
    {
        if (!$this->validator->valid($id))
            throw new InvalidArgumentException('id');
        return [$this->store->save($id), $this->notifier->notify($id)];
    }
}
$result = (new CrmFacade(new CrmValidator(), new CrmStore(), new CrmNotifier()))->process('lead-202');
expect($result === ['saved:lead-202', 'notified:lead-202'], 'facade workflow');
echo 'PASS kata 70' . PHP_EOL;
