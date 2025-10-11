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
            DeployerSeeder::class,
        ]);

        $this->seed(
            'Create the super admin user',
            fn (): User => $this->createSuperAdminUser(),
        );
    }

    private function createSuperAdminUser(): User
    {
        $user = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ]);

        $user->assignRole(Role::SUPER_ADMIN);

        return $user;
    }
}
