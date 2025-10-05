<?php

namespace Database\Seeders;

use App\Enums\Role as RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => RoleEnum::SUPER_ADMIN]);
        Role::create(['name' => RoleEnum::USER]);
    }
}
