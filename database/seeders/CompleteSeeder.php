<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class CompleteSeeder extends Seeder
{
    use HasSeedComments;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);

        $users = $this->seed(
            'Create 20 users',
            fn (): Collection => $this->createUsers(),
        );

        $this->seed(
            'Create 25 events for each user',
            fn () => $this->createEvents($users),
        );
    }

    /**
     * @return Collection<int, User>
     */
    private function createUsers(): Collection
    {
        $user = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ]);
        $user->assignRole(Role::SUPER_ADMIN);

        return User::factory(19)
            ->create()
            ->each(fn (User $user) => $user->assignRole(Role::USER))
            ->prepend($user);
    }

    /**
     * @param  Collection<int, User>  $users
     */
    private function createEvents(Collection $users): void
    {
        $users->each(fn (User $user) => Event::factory(25)
            ->for($user)
            ->create()
        );
    }
}
