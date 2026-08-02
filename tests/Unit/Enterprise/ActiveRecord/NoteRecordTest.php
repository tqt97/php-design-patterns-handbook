<?php

declare(strict_types=1);

namespace Tests\Unit\Enterprise\ActiveRecord;

use DesignPatterns\Enterprise\ActiveRecord\NoteRecord;
use PHPUnit\Framework\TestCase;

final class NoteRecordTest extends TestCase
{
    protected function setUp(): void
    {
        NoteRecord::reset();
    }

    public function test_it_persists_itself(): void
    {
        $note = new NoteRecord(1, 'Review the deployment checklist');
        $note->save();

        self::assertSame('Review the deployment checklist', NoteRecord::find(1)?->body);
    }
}
