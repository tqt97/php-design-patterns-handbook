<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class SearchValidator
{
    public function valid(string $id): bool
    {
        return $id !== '';
    }
}
final class SearchStore
{
    public function save(string $id): string
    {
        return 'saved:' . $id;
    }
}
final class SearchNotifier
{
    public function notify(string $id): string
    {
        return 'notified:' . $id;
    }
}
final class SearchFacade
{
    public function __construct(private SearchValidator $validator, private SearchStore $store, private SearchNotifier $notifier)
    {
    }
    public function process(string $id): array
    {
        if (!$this->validator->valid($id))
            throw new InvalidArgumentException('id');
        return [$this->store->save($id), $this->notifier->notify($id)];
    }
}
$result = (new SearchFacade(new SearchValidator(), new SearchStore(), new SearchNotifier()))->process('php-pattern');
expect($result === ['saved:php-pattern', 'notified:php-pattern'], 'facade workflow');
echo 'PASS kata 190' . PHP_EOL;
