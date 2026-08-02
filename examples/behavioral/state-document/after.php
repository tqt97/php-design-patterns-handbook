<?php

declare(strict_types=1);

interface DocumentState
{
	public function publish(Document $document): void;
}
final class DraftState implements DocumentState
{
	public function publish(Document $document): void
	{
		throw new RuntimeException('Must review first');
	}
}
final class ReviewState implements DocumentState
{
	public function publish(Document $document): void
	{
		$document->changeState(new PublishedState());
	}
}
final class PublishedState implements DocumentState
{
	public function publish(Document $document): void
	{
	}
}
final class Document
{
	public function __construct(private DocumentState $state)
	{
	}
	public function publish(): void
	{
		$this->state->publish($this);
	}
	public function changeState(DocumentState $state): void
	{
		$this->state = $state;
	}
	public function state(): string
	{
		return $this->state::class;
	}
}
$document = new Document(new ReviewState());
$document->publish();
echo $document->state() . PHP_EOL;
