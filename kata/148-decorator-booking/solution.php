<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface BookingOperation
{
    public function run(string $input): string;
}
final class CoreBookingOperation implements BookingOperation
{
    public function run(string $input): string
    {
        return strtolower(trim($input));
    }
}
final class TrimGuardBookingDecorator implements BookingOperation
{
    public function __construct(private BookingOperation $next)
    {
    }
    public function run(string $input): string
    {
        if (trim($input) === '')
            throw new InvalidArgumentException('empty');
        return $this->next->run($input);
    }
}
final class TaggedBookingDecorator implements BookingOperation
{
    public function __construct(private BookingOperation $next)
    {
    }
    public function run(string $input): string
    {
        return 'booking:' . $this->next->run($input);
    }
}
$op = new TaggedBookingDecorator(new TrimGuardBookingDecorator(new CoreBookingOperation()));
expect($op->run(' DEMO ') === 'booking:demo', 'decorator order');
echo 'PASS kata 148' . PHP_EOL;
