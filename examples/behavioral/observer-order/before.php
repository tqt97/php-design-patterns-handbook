<?php

declare(strict_types=1);

final class OrderService
{
    public function complete(int $orderId): void
    {
        echo "Save order {$orderId}\n";
        echo "Send email\n";
        echo "Update analytics\n";
    }
}
(new OrderService())->complete(1001);
