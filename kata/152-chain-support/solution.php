<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

abstract class SupportHandler
{
    private ?SupportHandler $next = null;
    public function setNext(SupportHandler $next): SupportHandler
    {
        $this->next = $next;
        return $next;
    }
    public function handle(array $request): string
    {
        return $this->next?->handle($request) ?? 'accepted';
    }
}
final class RequiredIdSupportHandler extends SupportHandler
{
    public function handle(array $r): string
    {
        return empty($r['id']) ? 'missing-id' : parent::handle($r);
    }
}
final class PrioritySupportHandler extends SupportHandler
{
    public function handle(array $r): string
    {
        return ($r['priority'] ?? 0) > 10 ? 'manual-review' : parent::handle($r);
    }
}
$first = new RequiredIdSupportHandler();
$first->setNext(new PrioritySupportHandler());
expect($first->handle(['id' => 'TICKET-88', 'priority' => 2]) === 'accepted', 'accepted');
expect($first->handle(['priority' => 2]) === 'missing-id', 'required');
echo 'PASS kata 152' . PHP_EOL;
