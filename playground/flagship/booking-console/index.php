<?php

declare(strict_types=1);

interface BookingRepository
{
    public function save(string $id): string;
}

final class InMemoryBookingRepository implements BookingRepository
{
    /** @var array<string, string> */
    private array $results = [];

    public function save(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return $this->results[$id] ??= 'booking-console:' . $id . ':ok';
    }
}

final readonly class BookingConsoleApplication
{
    public function __construct(private BookingRepository $port) {}

    public function run(string $id): string
    {
        return $this->port->save($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $app = new BookingConsoleApplication(new InMemoryBookingRepository());
    echo $app->run('demo'), PHP_EOL;
}
