<?php

declare(strict_types=1);

$status = 'draft';
$action = 'publish';
if ($status === 'draft' && $action === 'publish') {
	throw new RuntimeException('Must review first');
}
if ($status === 'review' && $action === 'publish') {
	$status = 'published';
}
echo $status . PHP_EOL;
