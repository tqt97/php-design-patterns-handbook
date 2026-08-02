<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class SearchRequest
{
    public function __construct(public string $id, public array $options, public bool $urgent)
    {
    }
}
final class SearchRequestBuilder
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
    public function build(): SearchRequest
    {
        if ($this->id === '')
            throw new DomainException('id required');
        return new SearchRequest($this->id, $this->options, $this->urgent);
    }
}
$request = (new SearchRequestBuilder())->identifiedBy('php-pattern')->withOption('source', 'web')->urgent()->build();
expect($request->id === 'php-pattern' && $request->urgent, 'built request');
echo 'PASS kata 105' . PHP_EOL;
