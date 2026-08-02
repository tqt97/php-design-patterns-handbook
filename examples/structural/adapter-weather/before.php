<?php

declare(strict_types=1);

final class LegacyWeatherApi
{
	public function temperatureFahrenheit(string $city): float
	{
		return 86.0;
	}
}
$api = new LegacyWeatherApi();
$celsius = ($api->temperatureFahrenheit('HCM') - 32) * 5 / 9;
echo $celsius . PHP_EOL;
