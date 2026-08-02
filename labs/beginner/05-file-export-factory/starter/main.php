<?php

declare(strict_types=1);

final class FileExportFactory
{
    public function execute(string $id): string
    {
        // TODO: refactor theo yêu cầu trong README.
        return 'todo:' . $id;
    }
}
