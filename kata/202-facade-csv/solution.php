<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class CsvValidator
{
    public function valid(string $id): bool
    {
        return $id !== '';
    }
}
final class CsvStore
{
    public function save(string $id): string
    {
        return 'saved:' . $id;
    }
}
final class CsvNotifier
{
    public function notify(string $id): string
    {
        return 'notified:' . $id;
    }
}
final class CsvFacade
{
    public function __construct(private CsvValidator $validator, private CsvStore $store, private CsvNotifier $notifier)
    {
    }
    public function process(string $id): array
    {
        if (!$this->validator->valid($id))
            throw new InvalidArgumentException('id');
        return [$this->store->save($id), $this->notifier->notify($id)];
    }
}
$result = (new CsvFacade(new CsvValidator(), new CsvStore(), new CsvNotifier()))->process('customers.csv');
expect($result === ['saved:customers.csv', 'notified:customers.csv'], 'facade workflow');
echo 'PASS kata 202' . PHP_EOL;
