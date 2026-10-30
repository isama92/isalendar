<?php

declare(strict_types=1);

namespace Tests;

use Database\Seeders\TestsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected bool $seed = true;

    protected string $seeder = TestsSeeder::class;
}
