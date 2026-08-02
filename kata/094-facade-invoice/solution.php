<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class InvoiceValidator
{
    public function valid(string $id): bool
    {
        return $id !== '';
    }
}
final class InvoiceStore
{
    public function save(string $id): string
    {
        return 'saved:' . $id;
    }
}
final class InvoiceNotifier
{
    public function notify(string $id): string
    {
        return 'notified:' . $id;
    }
}
final class InvoiceFacade
{
    public function __construct(private InvoiceValidator $validator, private InvoiceStore $store, private InvoiceNotifier $notifier)
    {
    }
    public function process(string $id): array
    {
        if (!$this->validator->valid($id))
            throw new InvalidArgumentException('id');
        return [$this->store->save($id), $this->notifier->notify($id)];
    }
}
$result = (new InvoiceFacade(new InvoiceValidator(), new InvoiceStore(), new InvoiceNotifier()))->process('INV-2026-001');
expect($result === ['saved:INV-2026-001', 'notified:INV-2026-001'], 'facade workflow');
echo 'PASS kata 94' . PHP_EOL;
