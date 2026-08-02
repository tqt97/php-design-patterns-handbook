<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$folders = [
    'benchmarks', 'cheatsheets', 'decisions', 'docs', 'examples', 'exercises',
    'framework-integration', 'handbook', 'interviews', 'kata', 'labs',
    'learning-path', 'playground', 'production', 'training',
];
$errors = [];
$checked = 0;
$allowedMermaid = ['flowchart', 'graph', 'sequenceDiagram', 'classDiagram', 'stateDiagram', 'stateDiagram-v2', 'erDiagram', 'journey', 'gantt', 'mindmap', 'timeline', 'quadrantChart', 'requirementDiagram', 'gitGraph', 'pie'];
$genericParticipants = ['FirstImplementation', 'SecondImplementation', 'ConcreteImplementation', 'GenericService', 'SomeManager'];

foreach ($folders as $folder) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/' . $folder));
    foreach ($iterator as $file) {
        if (! $file->isFile() || $file->getExtension() !== 'md') {
            continue;
        }
        ++$checked;
        $path = $file->getPathname();
        $relative = substr($path, strlen($root) + 1);
        $content = file_get_contents($path);
        if ($content === false) {
            $errors[] = "$relative: cannot read";
            continue;
        }
        if (! preg_match('/^#\s+\S+/m', $content)) {
            $errors[] = "$relative: missing H1";
        }
        if (substr_count($content, '```') % 2 !== 0) {
            $errors[] = "$relative: unbalanced fenced code blocks";
        }
        foreach ($genericParticipants as $name) {
            if (str_contains($content, $name)) {
                $errors[] = "$relative: generic participant '$name'";
            }
        }
        if (preg_match_all('/```mermaid\s*\n(.*?)```/s', $content, $matches)) {
            foreach ($matches[1] as $index => $diagram) {
                $diagram = trim($diagram);
                $firstLine = trim(strtok($diagram, "\n") ?: '');
                $type = strtok($firstLine, " \t") ?: '';
                if (! in_array($type, $allowedMermaid, true)) {
                    $errors[] = "$relative: mermaid block " . ($index + 1) . " has unsupported type '$type'";
                }
                if (strlen($diagram) < 40) {
                    $errors[] = "$relative: mermaid block " . ($index + 1) . ' is too small to explain a collaboration';
                }
                if (! preg_match('/(-->|->>|-->>|<\|--|<\|\.\.|==>|-->)/', $diagram) && ! str_starts_with($type, 'stateDiagram')) {
                    $errors[] = "$relative: mermaid block " . ($index + 1) . ' has no visible relation/flow';
                }
            }
        }
        if (preg_match_all('/```php\s*\n(.*?)```/s', $content, $phpBlocks)) {
            foreach ($phpBlocks[1] as $index => $block) {
                if (trim($block) === '') {
                    $errors[] = "$relative: empty PHP block " . ($index + 1);
                }
            }
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, "FAIL artifact alignment audit:\n- " . implode("\n- ", array_slice($errors, 0, 100)) . "\n");
    exit(1);
}

echo "PASS artifact alignment audit: {$checked} Markdown files; fences, Mermaid types and generic participants verified.\n";
