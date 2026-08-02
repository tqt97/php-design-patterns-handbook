<?php

declare(strict_types=1);

interface Renderer
{
	public function render(string $content): string;
}
final class HtmlRenderer implements Renderer
{
	public function render(string $content): string
	{
		return "<p>{$content}</p>";
	}
}
final class PdfRenderer implements Renderer
{
	public function render(string $content): string
	{
		return "PDF:{$content}";
	}
}
final class RendererFactory
{
	public function create(string $type): Renderer
	{
		return match ($type) { 'html' => new HtmlRenderer(), 'pdf' => new PdfRenderer(), default => throw new InvalidArgumentException('Unsupported renderer')};
	}
}
