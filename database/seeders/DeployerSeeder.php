<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DeployerSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProviderSeeder::class,
            RoleSeeder::class,
        ]);
    }
}
