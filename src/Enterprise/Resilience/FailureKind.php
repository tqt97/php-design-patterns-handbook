<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Resilience;

enum FailureKind: string
{
    case Transient = 'transient';
    case Permanent = 'permanent';
    case Ambiguous = 'ambiguous';
}
