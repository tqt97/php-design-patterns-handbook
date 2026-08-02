<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

abstract class CrmHandler
{
    private ?CrmHandler $next = null;
    public function setNext(CrmHandler $next): CrmHandler
    {
        $this->next = $next;
        return $next;
    }
    public function handle(array $request): string
    {
        return $this->next?->handle($request) ?? 'accepted';
    }
}
final class RequiredIdCrmHandler extends CrmHandler
{
    public function handle(array $r): string
    {
        return empty($r['id']) ? 'missing-id' : parent::handle($r);
    }
}
final class PriorityCrmHandler extends CrmHandler
{
    public function handle(array $r): string
    {
        return ($r['priority'] ?? 0) > 10 ? 'manual-review' : parent::handle($r);
    }
}
$first = new RequiredIdCrmHandler();
$first->setNext(new PriorityCrmHandler());
expect($first->handle(['id' => 'lead-202', 'priority' => 2]) === 'accepted', 'accepted');
expect($first->handle(['priority' => 2]) === 'missing-id', 'required');
echo 'PASS kata 104' . PHP_EOL;
