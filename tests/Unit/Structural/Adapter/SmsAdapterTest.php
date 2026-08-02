<?php

declare(strict_types=1);

namespace Tests\Unit\Structural\Adapter;

use DesignPatterns\Structural\Adapter\LegacySmsClient;
use DesignPatterns\Structural\Adapter\SmsAdapter;
use PHPUnit\Framework\TestCase;

final class SmsAdapterTest extends TestCase
{
    public function test_it_adapts_legacy_client(): void
    {
        (new SmsAdapter(new LegacySmsClient()))->send('0900000000', 'Hello');
        self::assertTrue(true);
    }
}
