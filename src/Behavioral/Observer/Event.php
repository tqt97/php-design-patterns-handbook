<?php

declare(strict_types=1);

namespace DesignPatterns\Behavioral\Observer;

interface Event
{
    public function name(): string;
}
