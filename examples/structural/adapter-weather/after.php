<?php

declare(strict_types=1);

interface WeatherProvider
{
	public function temperatureCelsius(string $city): float;
}
final class LegacyWeatherApi
{
	public function temperatureFahrenheit(string $city): float
	{
		return 86.0;
	}
}
final class LegacyWeatherAdapter implements WeatherProvider
{
	public function __construct(private LegacyWeatherApi $api)
	{
	}
	public function temperatureCelsius(string $city): float
	{
		return ($this->api->temperatureFahrenheit($city) - 32) * 5 / 9;
	}
}
echo (new LegacyWeatherAdapter(new LegacyWeatherApi()))->temperatureCelsius('HCM') . PHP_EOL;
