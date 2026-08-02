<?php

declare(strict_types=1);

$isVip = true;
$total = 600000;
if ($isVip && $total >= 500000) {
	echo 'Eligible';
}
