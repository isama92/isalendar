<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestsSeeder extends Seeder
{
    public function run(): void
    {
        DB::query()->raw('SET GLOBAL time_zone = \'+00:00\';');

        $this->call([
            ProviderSeeder::class,
            RoleSeeder::class,
        ]);
    }
}
