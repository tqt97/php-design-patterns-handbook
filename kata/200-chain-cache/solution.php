<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

abstract class CacheHandler
{
    private ?CacheHandler $next = null;
    public function setNext(CacheHandler $next): CacheHandler
    {
        $this->next = $next;
        return $next;
    }
    public function handle(array $request): string
    {
        return $this->next?->handle($request) ?? 'accepted';
    }
}
final class RequiredIdCacheHandler extends CacheHandler
{
    public function handle(array $r): string
    {
        return empty($r['id']) ? 'missing-id' : parent::handle($r);
    }
}
final class PriorityCacheHandler extends CacheHandler
{
    public function handle(array $r): string
    {
        return ($r['priority'] ?? 0) > 10 ? 'manual-review' : parent::handle($r);
    }
}
$first = new RequiredIdCacheHandler();
$first->setNext(new PriorityCacheHandler());
expect($first->handle(['id' => 'customer:42', 'priority' => 2]) === 'accepted', 'accepted');
expect($first->handle(['priority' => 2]) === 'missing-id', 'required');
echo 'PASS kata 200' . PHP_EOL;
