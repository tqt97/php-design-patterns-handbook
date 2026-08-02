<?php

declare(strict_types=1);

namespace Tests\Architecture;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class StrictTypesTest extends TestCase
{
    public function test_all_source_files_enable_strict_types(): void
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__.'/../../src', FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') { continue; }
            $content = file_get_contents($file->getPathname());
            self::assertIsString($content);
            self::assertStringContainsString('declare(strict_types=1);', $content, $file->getPathname());
        }
    }
}
