<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Adapter;

final class LegacySmsClient
{
    public function transmit(string $phone, string $body): bool
    {
        return $phone !== '' && $body !== '';
    }
}
