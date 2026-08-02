<?php

declare(strict_types=1);

final class OrderDraft
{
    /** @param list<string> $steps */
    public function __construct(public array $steps = [])
    {
    }
}

interface Pipe
{
    public function handle(OrderDraft $draft, callable $next): OrderDraft;
}

final class ValidateOrder implements Pipe
{
    public function handle(OrderDraft $draft, callable $next): OrderDraft
    {
        $draft->steps[] = 'validated';

        return $next($draft);
    }
}

final class ReserveInventory implements Pipe
{
    public function handle(OrderDraft $draft, callable $next): OrderDraft
    {
        $draft->steps[] = 'inventory_reserved';

        return $next($draft);
    }
}

final class ApplyDiscount implements Pipe
{
    public function handle(OrderDraft $draft, callable $next): OrderDraft
    {
        $draft->steps[] = 'discount_applied';

        return $next($draft);
    }
}

final class Pipeline
{
    /** @param list<Pipe> $pipes */
    public function process(OrderDraft $draft, array $pipes): OrderDraft
    {
        $destination = static fn(OrderDraft $value): OrderDraft => $value;

        $chain = array_reduce(
            array_reverse($pipes),
            static fn(callable $next, Pipe $pipe): callable =>
            static fn(OrderDraft $value): OrderDraft => $pipe->handle($value, $next),
            $destination,
        );

        return $chain($draft);
    }
}

$result = (new Pipeline())->process(new OrderDraft(), [
    new ValidateOrder(),
    new ReserveInventory(),
    new ApplyDiscount(),
]);

echo implode(' -> ', $result->steps) . PHP_EOL;
