<?php

declare(strict_types=1);

$message = 'Không đăng nhập được';
if (str_contains($message, 'thanh toán')) {
	echo 'Billing';
} elseif (str_contains($message, 'đăng nhập')) {
	echo 'Technical';
} else {
	echo 'General';
}
