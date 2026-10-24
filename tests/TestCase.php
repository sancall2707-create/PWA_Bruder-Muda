<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Proteksi mutlak: Pengujian dilarang keras menyentuh database fisik database.sqlite
        $db = config('database.connections.sqlite.database');
        if (config('database.default') === 'sqlite' && $db !== ':memory:' && ! str_ends_with((string) $db, ':memory:')) {
            throw new \RuntimeException("BAHAYA: Pengujian dilarang berjalan pada database fisik ({$db})! Hapus cache config terlebih dahulu.");
        }
    }
}
