<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class UserRequest
{
    public function __construct(public string $id, public array $options, public bool $urgent)
    {
    }
}
final class UserRequestBuilder
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
    public function build(): UserRequest
    {
        if ($this->id === '')
            throw new DomainException('id required');
        return new UserRequest($this->id, $this->options, $this->urgent);
    }
}
$request = (new UserRequestBuilder())->identifiedBy('user-42')->withOption('source', 'web')->urgent()->build();
expect($request->id === 'user-42' && $request->urgent, 'built request');
echo 'PASS kata 201' . PHP_EOL;
