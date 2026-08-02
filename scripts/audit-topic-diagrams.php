<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$failures = [];

$checks = [
    ['root' => 'kata', 'heading' => '## Sơ đồ mục tiêu', 'expected' => 204, 'forbidden' => ['Primary Variant', 'Extension / Failure Variant', 'Domain Outcome']],
    ['root' => 'framework-integration', 'heading' => '## Phân tích production sâu hơn', 'expected' => 14, 'forbidden' => ['Framework entrypoint', 'Application boundary', 'External dependency']],
    ['root' => 'handbook', 'heading' => '## Mental model', 'expected' => 72, 'forbidden' => ['Consumer / Use case', 'Boundary của ', 'Detail có thể thay đổi']],
];

foreach ($checks as $check) {
    $directory = $root . '/' . $check['root'];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    $count = 0;
    $sections = [];

    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'md') {
            continue;
        }

        $content = file_get_contents($file->getPathname());
        if ($content === false || !str_contains($content, $check['heading'])) {
            continue;
        }

        ++$count;
        $after = explode($check['heading'], $content, 2)[1];
        $section = preg_split('/\n## /', $after, 2)[0] ?? '';

        if (!str_contains($section, '```mermaid')) {
            $failures[] = $file->getPathname() . ': missing Mermaid diagram in ' . $check['heading'];
        }

        foreach ($check['forbidden'] as $generic) {
            if (str_contains($section, $generic)) {
                $failures[] = $file->getPathname() . ': generic diagram token remains: ' . $generic;
            }
        }

        $normalized = preg_replace('/\s+/', ' ', trim($section));
        if (is_string($normalized)) {
            $sections[$normalized][] = $file->getPathname();
        }
    }

    if ($count !== $check['expected']) {
        $failures[] = sprintf('%s: expected %d diagram sections, found %d', $check['root'], $check['expected'], $count);
    }

    foreach ($sections as $files) {
        if (count($files) > 1) {
            $failures[] = $check['root'] . ': duplicate topic diagram sections: ' . implode(', ', $files);
        }
    }
}

if ($failures !== []) {
    fwrite(STDERR, "FAIL topic diagram audit:\n- " . implode("\n- ", $failures) . "\n");
    exit(1);
}

fwrite(STDOUT, "PASS topic diagram audit: kata, framework integration and handbook diagrams are topic-specific.\n");
