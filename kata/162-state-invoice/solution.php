<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface InvoiceState
{
    public function advance(InvoiceWorkflow $workflow): void;
    public function name(): string;
}
final class DraftInvoiceState implements InvoiceState
{
    public function advance(InvoiceWorkflow $w): void
    {
        $w->transitionTo(new ActiveInvoiceState());
    }
    public function name(): string
    {
        return 'draft';
    }
}
final class ActiveInvoiceState implements InvoiceState
{
    public function advance(InvoiceWorkflow $w): void
    {
        $w->transitionTo(new CompletedInvoiceState());
    }
    public function name(): string
    {
        return 'active';
    }
}
final class CompletedInvoiceState implements InvoiceState
{
    public function advance(InvoiceWorkflow $w): void
    {
        throw new DomainException('completed');
    }
    public function name(): string
    {
        return 'completed';
    }
}
final class InvoiceWorkflow
{
    public function __construct(private InvoiceState $state)
    {
    }
    public function advance(): void
    {
        $this->state->advance($this);
    }
    public function transitionTo(InvoiceState $s): void
    {
        $this->state = $s;
    }
    public function status(): string
    {
        return $this->state->name();
    }
}
$w = new InvoiceWorkflow(new DraftInvoiceState());
$w->advance();
expect($w->status() === 'active', 'first transition');
$w->advance();
expect($w->status() === 'completed', 'second transition');
echo 'PASS kata 162' . PHP_EOL;
