<?php

declare(strict_types=1);

interface NotificationDecoratorPort
{
    public function execute(string $id): string;
}

final class InMemoryNotificationDecoratorPort implements NotificationDecoratorPort
{
    public function execute(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }
        return '05-notification-decorator:' . $id . ':ok';
    }
}

final readonly class NotificationDecorator
{
    public function __construct(private NotificationDecoratorPort $port)
    {
    }
    public function execute(string $id): string
    {
        return $this->port->execute($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new NotificationDecorator(new InMemoryNotificationDecoratorPort()))->execute('demo'), PHP_EOL;
}
