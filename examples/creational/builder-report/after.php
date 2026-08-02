<?php

declare(strict_types=1);

final class Report
{
	public function __construct(public string $title, public bool $chart, public bool $summary, public ?string $footer)
	{
	}
}
final class ReportBuilder
{
	private string $title = '';
	private bool $chart = false;
	private bool $summary = false;
	private ?string $footer = null;
	public function title(string $value): self
	{
		$this->title = $value;
		return $this;
	}
	public function withChart(): self
	{
		$this->chart = true;
		return $this;
	}
	public function withSummary(): self
	{
		$this->summary = true;
		return $this;
	}
	public function footer(string $value): self
	{
		$this->footer = $value;
		return $this;
	}
	public function build(): Report
	{
		return new Report($this->title, $this->chart, $this->summary, $this->footer);
	}
}
$report = (new ReportBuilder())->title('Monthly')->withChart()->withSummary()->footer('Internal')->build();
var_dump($report);
