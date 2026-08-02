<?php

declare(strict_types=1);
require dirname(__DIR__) . '/Benchmark.php';

interface Formatter
{
    public function format(string $value): string;
}
class PlainFormatter implements Formatter
{
    public function format(string $value): string
    {
        return $value;
    }
}
final class FixedFormatter extends PlainFormatter
{
    public function format(string $value): string
    {
        return '[' . strtoupper(trim(parent::format($value))) . ']';
    }
}
abstract class FormatterDecorator implements Formatter
{
    public function __construct(protected Formatter $next)
    {
    }
}
final class TrimDecorator extends FormatterDecorator
{
    public function format(string $value): string
    {
        return trim($this->next->format($value));
    }
}
final class UppercaseDecorator extends FormatterDecorator
{
    public function format(string $value): string
    {
        return strtoupper($this->next->format($value));
    }
}
final class BracketDecorator extends FormatterDecorator
{
    public function format(string $value): string
    {
        return '[' . $this->next->format($value) . ']';
    }
}

$fixed = new FixedFormatter();
$decorated = new BracketDecorator(new UppercaseDecorator(new TrimDecorator(new PlainFormatter())));
$input = '  invoice  ';
$results = [
    'fixed inheritance' => Benchmark::measure(fn(): string => $fixed->format($input)),
    '3 decorators' => Benchmark::measure(fn(): string => $decorated->format($input)),
];
Benchmark::report('Decorator vs Inheritance', $results);
