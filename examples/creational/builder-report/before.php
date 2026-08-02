<?php

declare(strict_types=1);

final class Report
{
	public function __construct(public string $title, public bool $chart, public bool $summary, public ?string $footer)
	{
	}
}
$report = new Report('Monthly', true, true, 'Internal');
var_dump($report);
