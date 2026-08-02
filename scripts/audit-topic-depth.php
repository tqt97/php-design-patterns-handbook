<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$errors = [];

$rules = [
    'docs/00-foundations' => ['minWords' => 450, 'diagram' => false],
    'docs/01-creational' => ['minWords' => 650, 'diagram' => true],
    'docs/02-structural' => ['minWords' => 650, 'diagram' => true],
    'docs/03-behavioral' => ['minWords' => 650, 'diagram' => true],
    'docs/04-enterprise-patterns' => ['minWords' => 550, 'diagram' => true],
    'docs/09-expert-practice' => ['minWords' => 300, 'diagram' => true],
    'production' => ['minWords' => 350, 'diagram' => true],
    'framework-integration' => ['minWords' => 300, 'diagram' => false],
];

foreach ($rules as $directory => $rule) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/' . $directory));
    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'md' || $file->getFilename() === 'README.md') {
            continue;
        }
        $path = $file->getPathname();
        $content = (string) file_get_contents($path);
        $withoutCode = preg_replace('/```.*?```/s', ' ', $content) ?? $content;
        preg_match_all('/[\p{L}\p{N}_-]+/u', $withoutCode, $matches);
        $words = count($matches[0]);
        if ($words < $rule['minWords']) {
            $errors[] = sprintf('%s has %d words; expected at least %d', str_replace($root . '/', '', $path), $words, $rule['minWords']);
        }
        if ($rule['diagram'] && !str_contains($content, '```mermaid')) {
            $errors[] = sprintf('%s requires a Mermaid diagram', str_replace($root . '/', '', $path));
        }
    }
}

$keyReadmes = [
    'interviews/README.md',
    'docs/06-case-studies/README.md',
    'training/level-01-foundations/README.md',
    'training/level-02-core-patterns/README.md',
    'training/level-03-enterprise/README.md',
    'training/level-04-architecture/README.md',
    'training/level-05-tech-lead/README.md',
];
foreach ($keyReadmes as $relative) {
    $content = (string) file_get_contents($root . '/' . $relative);
    preg_match_all('/[\p{L}\p{N}_-]+/u', $content, $matches);
    if (count($matches[0]) < 220) {
        $errors[] = $relative . ' is too thin for a key navigation page';
    }
}

if ($errors !== []) {
    fwrite(STDERR, "FAIL topic depth audit:\n- " . implode("\n- ", $errors) . "\n");
    exit(1);
}

echo "PASS topic depth audit: core folders meet depth, diagram and navigation requirements.\n";
