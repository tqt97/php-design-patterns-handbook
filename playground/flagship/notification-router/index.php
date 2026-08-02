<?php

declare(strict_types=1);

interface NotificationChannel
{
    public function send(string $id): string;
}

final class InMemoryNotificationChannel implements NotificationChannel
{
    /** @var array<string, string> */
    private array $results = [];

    public function send(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return $this->results[$id] ??= 'notification-router:' . $id . ':ok';
    }
}

final readonly class NotificationRouterApplication
{
    public function __construct(private NotificationChannel $port) {}

    public function run(string $id): string
    {
        return $this->port->send($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $app = new NotificationRouterApplication(new InMemoryNotificationChannel());
    echo $app->run('demo'), PHP_EOL;
}
