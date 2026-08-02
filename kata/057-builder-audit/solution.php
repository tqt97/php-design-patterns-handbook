<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class AuditRequest
{
    public function __construct(public string $id, public array $options, public bool $urgent)
    {
    }
}
final class AuditRequestBuilder
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
    public function build(): AuditRequest
    {
        if ($this->id === '')
            throw new DomainException('id required');
        return new AuditRequest($this->id, $this->options, $this->urgent);
    }
}
$request = (new AuditRequestBuilder())->identifiedBy('user.updated')->withOption('source', 'web')->urgent()->build();
expect($request->id === 'user.updated' && $request->urgent, 'built request');
echo 'PASS kata 57' . PHP_EOL;
