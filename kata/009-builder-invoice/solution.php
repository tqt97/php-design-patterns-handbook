<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class InvoiceRequest
{
    public function __construct(public string $id, public array $options, public bool $urgent)
    {
    }
}
final class InvoiceRequestBuilder
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
    public function build(): InvoiceRequest
    {
        if ($this->id === '')
            throw new DomainException('id required');
        return new InvoiceRequest($this->id, $this->options, $this->urgent);
    }
}
$request = (new InvoiceRequestBuilder())->identifiedBy('INV-2026-001')->withOption('source', 'web')->urgent()->build();
expect($request->id === 'INV-2026-001' && $request->urgent, 'built request');
echo 'PASS kata 9' . PHP_EOL;
