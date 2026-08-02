<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface BookingState
{
    public function advance(BookingWorkflow $workflow): void;
    public function name(): string;
}
final class DraftBookingState implements BookingState
{
    public function advance(BookingWorkflow $w): void
    {
        $w->transitionTo(new ActiveBookingState());
    }
    public function name(): string
    {
        return 'draft';
    }
}
final class ActiveBookingState implements BookingState
{
    public function advance(BookingWorkflow $w): void
    {
        $w->transitionTo(new CompletedBookingState());
    }
    public function name(): string
    {
        return 'active';
    }
}
final class CompletedBookingState implements BookingState
{
    public function advance(BookingWorkflow $w): void
    {
        throw new DomainException('completed');
    }
    public function name(): string
    {
        return 'completed';
    }
}
final class BookingWorkflow
{
    public function __construct(private BookingState $state)
    {
    }
    public function advance(): void
    {
        $this->state->advance($this);
    }
    public function transitionTo(BookingState $s): void
    {
        $this->state = $s;
    }
    public function status(): string
    {
        return $this->state->name();
    }
}
$w = new BookingWorkflow(new DraftBookingState());
$w->advance();
expect($w->status() === 'active', 'first transition');
$w->advance();
expect($w->status() === 'completed', 'second transition');
echo 'PASS kata 114' . PHP_EOL;
