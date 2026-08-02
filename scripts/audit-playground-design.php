<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$files = glob($root . '/playground/[0-9]*-*/README.md') ?: [];
$diagrams = [];
$errors = [];

$patternTerms = [
    'strategy' => ['policy', 'strategy'],
    'factory' => ['creator', 'factory method'],
    'adapter' => ['adapter', 'translate'],
    'observer' => ['dispatcher', 'subscriber'],
    'decorator' => ['decorator', 'wrapper'],
    'state' => ['state', 'transition'],
    'command' => ['command', 'handler'],
    'chain' => ['handler', 'short-circuit'],
    'builder' => ['builder', 'build'],
    'facade' => ['facade', 'subsystem'],
    'proxy' => ['proxy', 'delegate'],
    'repository' => ['repository', 'persistence'],
];

foreach ($files as $file) {
    $dir = basename(dirname($file));
    if (!preg_match('/^\d+-(\w+)-(\w+)$/', $dir, $m)) {
        continue;
    }
    [, $pattern, $domain] = $m;
    $content = file_get_contents($file) ?: '';
    $lower = strtolower($content);

    if (!str_contains($lower, $pattern) || !str_contains($lower, $domain)) {
        $errors[] = "$dir: README does not mention both pattern and domain";
    }

    if (!preg_match('/```mermaid\s+(.*?)```/s', $content, $diagramMatch)) {
        $errors[] = "$dir: missing Mermaid diagram";
        continue;
    }

    $diagram = trim(preg_replace('/\s+/', ' ', $diagramMatch[1]) ?? '');
    $diagrams[$diagram][] = $dir;

    $terms = $patternTerms[$pattern] ?? [];
    $matched = false;
    foreach ($terms as $term) {
        if (str_contains(strtolower($diagram), $term) || str_contains($lower, $term)) {
            $matched = true;
            break;
        }
    }
    if (!$matched) {
        $errors[] = "$dir: diagram/content does not expose the mechanism of $pattern";
    }

    if (str_contains($diagram, 'Domain-specific implementation') || str_contains($diagram, 'Client use case')) {
        $errors[] = "$dir: generic participant remains in diagram";
    }
}

foreach ($diagrams as $diagram => $owners) {
    if (count($owners) > 1) {
        $errors[] = 'Duplicate Mermaid diagram: ' . implode(', ', $owners);
    }
}

if ($errors !== []) {
    fwrite(STDERR, "FAIL playground design audit\n- " . implode("\n- ", $errors) . "\n");
    exit(1);
}

echo 'PASS playground design audit: ' . count($files) . " drill README files have unique, topic-aligned diagrams.\n";
