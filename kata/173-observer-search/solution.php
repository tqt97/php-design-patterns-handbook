<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class SearchEvent
{
    public function __construct(public string $id)
    {
    }
}
interface SearchListener
{
    public function handle(SearchEvent $event): void;
}
final class RecordingSearchListener implements SearchListener
{
    public array $seen = [];
    public function handle(SearchEvent $event): void
    {
        $this->seen[] = $event->id;
    }
}
final class SearchPublisher
{
    private array $listeners = [];
    public function subscribe(SearchListener $l): void
    {
        $this->listeners[] = $l;
    }
    public function publish(SearchEvent $e): void
    {
        foreach ($this->listeners as $l)
            $l->handle($e);
    }
}
$a = new RecordingSearchListener();
$b = new RecordingSearchListener();
$publisher = new SearchPublisher();
$publisher->subscribe($a);
$publisher->subscribe($b);
$publisher->publish(new SearchEvent('php-pattern'));
expect($a->seen === ['php-pattern'] && $b->seen === ['php-pattern'], 'all listeners notified');
echo 'PASS kata 173' . PHP_EOL;
