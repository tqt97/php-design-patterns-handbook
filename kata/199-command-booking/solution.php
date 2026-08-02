<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface BookingCommand
{
    public function execute(): string;
}
final class CreateBookingCommand implements BookingCommand
{
    public function __construct(private string $id)
    {
    }
    public function execute(): string
    {
        return 'created:' . $this->id;
    }
}
final class BookingCommandBus
{
    public array $audit = [];
    public function dispatch(BookingCommand $command): string
    {
        $result = $command->execute();
        $this->audit[] = $result;
        return $result;
    }
}
$bus = new BookingCommandBus();
expect($bus->dispatch(new CreateBookingCommand('room-12')) === 'created:room-12', 'command result');
expect(count($bus->audit) === 1, 'audit');
echo 'PASS kata 199' . PHP_EOL;
