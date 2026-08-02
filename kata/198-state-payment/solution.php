<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface PaymentState
{
    public function advance(PaymentWorkflow $workflow): void;
    public function name(): string;
}
final class DraftPaymentState implements PaymentState
{
    public function advance(PaymentWorkflow $w): void
    {
        $w->transitionTo(new ActivePaymentState());
    }
    public function name(): string
    {
        return 'draft';
    }
}
final class ActivePaymentState implements PaymentState
{
    public function advance(PaymentWorkflow $w): void
    {
        $w->transitionTo(new CompletedPaymentState());
    }
    public function name(): string
    {
        return 'active';
    }
}
final class CompletedPaymentState implements PaymentState
{
    public function advance(PaymentWorkflow $w): void
    {
        throw new DomainException('completed');
    }
    public function name(): string
    {
        return 'completed';
    }
}
final class PaymentWorkflow
{
    public function __construct(private PaymentState $state)
    {
    }
    public function advance(): void
    {
        $this->state->advance($this);
    }
    public function transitionTo(PaymentState $s): void
    {
        $this->state = $s;
    }
    public function status(): string
    {
        return $this->state->name();
    }
}
$w = new PaymentWorkflow(new DraftPaymentState());
$w->advance();
expect($w->status() === 'active', 'first transition');
$w->advance();
expect($w->status() === 'completed', 'second transition');
echo 'PASS kata 198' . PHP_EOL;
