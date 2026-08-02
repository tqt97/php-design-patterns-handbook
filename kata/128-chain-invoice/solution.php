<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

abstract class InvoiceHandler
{
    private ?InvoiceHandler $next = null;
    public function setNext(InvoiceHandler $next): InvoiceHandler
    {
        $this->next = $next;
        return $next;
    }
    public function handle(array $request): string
    {
        return $this->next?->handle($request) ?? 'accepted';
    }
}
final class RequiredIdInvoiceHandler extends InvoiceHandler
{
    public function handle(array $r): string
    {
        return empty($r['id']) ? 'missing-id' : parent::handle($r);
    }
}
final class PriorityInvoiceHandler extends InvoiceHandler
{
    public function handle(array $r): string
    {
        return ($r['priority'] ?? 0) > 10 ? 'manual-review' : parent::handle($r);
    }
}
$first = new RequiredIdInvoiceHandler();
$first->setNext(new PriorityInvoiceHandler());
expect($first->handle(['id' => 'INV-2026-001', 'priority' => 2]) === 'accepted', 'accepted');
expect($first->handle(['priority' => 2]) === 'missing-id', 'required');
echo 'PASS kata 128' . PHP_EOL;
