<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Resilience;

use RuntimeException;

final class CircuitOpenException extends RuntimeException
{
}
