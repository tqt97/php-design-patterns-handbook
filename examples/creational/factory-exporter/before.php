<?php

declare(strict_types=1);

$format = 'json';
if ($format === 'json') {
	echo json_encode(['name' => 'Tuan']);
} elseif ($format === 'csv') {
	echo "name\nTuan";
} else {
	throw new InvalidArgumentException('Unsupported format');
}
