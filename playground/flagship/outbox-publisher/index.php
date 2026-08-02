<?php

declare(strict_types=1);

interface Publisher
{
    public function publish(string $id): string;
}

final class InMemoryPublisher implements Publisher
{
    /** @var array<string, string> */
    private array $results = [];

    public function publish(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return $this->results[$id] ??= 'outbox-publisher:' . $id . ':ok';
    }
}

final readonly class OutboxPublisherApplication
{
    public function __construct(private Publisher $port) {}

    public function run(string $id): string
    {
        return $this->port->publish($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $app = new OutboxPublisherApplication(new InMemoryPublisher());
    echo $app->run('demo'), PHP_EOL;
}
