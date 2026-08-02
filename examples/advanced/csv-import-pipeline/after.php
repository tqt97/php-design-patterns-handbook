<?php

declare(strict_types=1);

interface ImportStep
{
    public function process(string $id): string;
}

final class InMemoryImportStep implements ImportStep
{
    public function process(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return 'import:' . $id . ':ok';
    }
}

final readonly class CSVImportPipelineUseCase
{
    public function __construct(private ImportStep $component) {}

    public function handle(string $id): string
    {
        return $this->component->process($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new CSVImportPipelineUseCase(new InMemoryImportStep()))->handle('demo-1'), PHP_EOL;
}
