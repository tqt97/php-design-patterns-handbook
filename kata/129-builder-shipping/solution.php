<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class ShippingRequest
{
    public function __construct(public string $id, public array $options, public bool $urgent)
    {
    }
}
final class ShippingRequestBuilder
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
    public function build(): ShippingRequest
    {
        if ($this->id === '')
            throw new DomainException('id required');
        return new ShippingRequest($this->id, $this->options, $this->urgent);
    }
}
$request = (new ShippingRequestBuilder())->identifiedBy('HCM-HN')->withOption('source', 'web')->urgent()->build();
expect($request->id === 'HCM-HN' && $request->urgent, 'built request');
echo 'PASS kata 129' . PHP_EOL;
