<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

abstract class ShippingHandler
{
    private ?ShippingHandler $next = null;
    public function setNext(ShippingHandler $next): ShippingHandler
    {
        $this->next = $next;
        return $next;
    }
    public function handle(array $request): string
    {
        return $this->next?->handle($request) ?? 'accepted';
    }
}
final class RequiredIdShippingHandler extends ShippingHandler
{
    public function handle(array $r): string
    {
        return empty($r['id']) ? 'missing-id' : parent::handle($r);
    }
}
final class PriorityShippingHandler extends ShippingHandler
{
    public function handle(array $r): string
    {
        return ($r['priority'] ?? 0) > 10 ? 'manual-review' : parent::handle($r);
    }
}
$first = new RequiredIdShippingHandler();
$first->setNext(new PriorityShippingHandler());
expect($first->handle(['id' => 'HCM-HN', 'priority' => 2]) === 'accepted', 'accepted');
expect($first->handle(['priority' => 2]) === 'missing-id', 'required');
echo 'PASS kata 44' . PHP_EOL;
