<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class OrderRequest
{
    public function __construct(public string $id, public array $options, public bool $urgent)
    {
    }
}
final class OrderRequestBuilder
{
    private string $id = '';
    private array $options = [];
    private bool $urgent = false;
    public function identifiedBy(string $id): self
    {
        $this->id = $id;
        return $this;
    }
    public function withOption(string $key, string $value): self
    {
        $this->options[$key] = $value;
        return $this;
    }
    public function urgent(): self
    {
        $this->urgent = true;
        return $this;
    }
    public function build(): OrderRequest
    {
        if ($this->id === '')
            throw new DomainException('id required');
        return new OrderRequest($this->id, $this->options, $this->urgent);
    }
}
$request = (new OrderRequestBuilder())->identifiedBy('ORD-1001')->withOption('source', 'web')->urgent()->build();
expect($request->id === 'ORD-1001' && $request->urgent, 'built request');
echo 'PASS kata 21' . PHP_EOL;
