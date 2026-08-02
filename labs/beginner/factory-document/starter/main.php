<?php

declare(strict_types=1);

$type = 'html';
$renderer = $type === 'html' ? new HtmlRenderer() : new PdfRenderer();
