<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    protected function beforeRefreshingDatabase()
    {
        $this->ensureSafeTestDatabase();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureSafeTestDatabase();
    }

    protected function ensureSafeTestDatabase(): void
    {
        $activeDb = config('database.connections.'.config('database.default').'.database');
        if ($activeDb !== 'simonka_test') {
            throw new RuntimeException(
                "SAFETY GUARD BLOCKED EXECUTION: Active database is '{$activeDb}'. Tests are strictly restricted to 'simonka_test' to prevent database mutation or data loss!"
            );
        }
    }
}
