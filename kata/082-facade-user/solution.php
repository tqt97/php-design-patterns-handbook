<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class UserValidator
{
    public function valid(string $id): bool
    {
        return $id !== '';
    }
}
final class UserStore
{
    public function save(string $id): string
    {
        return 'saved:' . $id;
    }
}
final class UserNotifier
{
    public function notify(string $id): string
    {
        return 'notified:' . $id;
    }
}
final class UserFacade
{
    public function __construct(private UserValidator $validator, private UserStore $store, private UserNotifier $notifier)
    {
    }
    public function process(string $id): array
    {
        if (!$this->validator->valid($id))
            throw new InvalidArgumentException('id');
        return [$this->store->save($id), $this->notifier->notify($id)];
    }
}
$result = (new UserFacade(new UserValidator(), new UserStore(), new UserNotifier()))->process('user-42');
expect($result === ['saved:user-42', 'notified:user-42'], 'facade workflow');
echo 'PASS kata 82' . PHP_EOL;
