<?php

declare(strict_types=1);

namespace DesignPatterns\Behavioral\State;

final class Order
{
    public function __construct(private OrderState $state = new PendingState())
    {
    }

    public function pay(): void
    {
        $this->state->pay($this);
    }

    public function ship(): void
    {
        $this->state->ship($this);
    }

    public function cancel(): void
    {
        $this->state->cancel($this);
    }

    public function state(): string
    {
        return $this->state->label();
    }


    public function transitionTo(OrderState $state): void
    {
        $this->state = $state;
    }
}
