<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class EmailValidator
{
    public function valid(string $id): bool
    {
        return $id !== '';
    }
}
final class EmailStore
{
    public function save(string $id): string
    {
        return 'saved:' . $id;
    }
}
final class EmailNotifier
{
    public function notify(string $id): string
    {
        return 'notified:' . $id;
    }
}
final class EmailFacade
{
    public function __construct(private EmailValidator $validator, private EmailStore $store, private EmailNotifier $notifier)
    {
    }
    public function process(string $id): array
    {
        if (!$this->validator->valid($id))
            throw new InvalidArgumentException('id');
        return [$this->store->save($id), $this->notifier->notify($id)];
    }
}
$result = (new EmailFacade(new EmailValidator(), new EmailStore(), new EmailNotifier()))->process('welcome@example.com');
expect($result === ['saved:welcome@example.com', 'notified:welcome@example.com'], 'facade workflow');
echo 'PASS kata 178' . PHP_EOL;
