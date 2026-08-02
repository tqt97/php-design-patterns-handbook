<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class ReportValidator
{
    public function valid(string $id): bool
    {
        return $id !== '';
    }
}
final class ReportStore
{
    public function save(string $id): string
    {
        return 'saved:' . $id;
    }
}
final class ReportNotifier
{
    public function notify(string $id): string
    {
        return 'notified:' . $id;
    }
}
final class ReportFacade
{
    public function __construct(private ReportValidator $validator, private ReportStore $store, private ReportNotifier $notifier)
    {
    }
    public function process(string $id): array
    {
        if (!$this->validator->valid($id))
            throw new InvalidArgumentException('id');
        return [$this->store->save($id), $this->notifier->notify($id)];
    }
}
$result = (new ReportFacade(new ReportValidator(), new ReportStore(), new ReportNotifier()))->process('sales-monthly');
expect($result === ['saved:sales-monthly', 'notified:sales-monthly'], 'facade workflow');
echo 'PASS kata 58' . PHP_EOL;
