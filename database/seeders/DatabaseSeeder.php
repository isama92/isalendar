<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use HasSeedComments;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);

        $this->seed(
            'Create the super admin user',
            fn (): User => $this->createSuperAdminUser(),
        );
    }

    private function createSuperAdminUser(): User
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $user->assignRole(Role::SUPER_ADMIN);

        return $user;
    }
}
