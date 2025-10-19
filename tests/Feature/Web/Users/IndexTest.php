<?php

use App\Enums\Role;
use App\Models\User;

use function Pest\Laravel\get;

uses()->group('web', 'users');

it('redirects unauthenticated users to login', function (): void {
    get(route('users.index'))
        ->assertRedirect(route('login'));
});

it('prevents regular users from accessing users index', function (): void {
    actingAsUser();

    get(route('users.index'))
        ->assertForbidden();
});

it('allows super admins to view users index', function (): void {
    actingAsAdmin();

    get(route('users.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Users/Index')
            ->has('users')
            ->has('roles')
            ->where('roles.0.value', Role::USER->value)
            ->where('roles.0.label', Role::USER->label())
            ->where('roles.1.value', Role::SUPER_ADMIN->value)
            ->where('roles.1.label', Role::SUPER_ADMIN->label())
        );
});

it('shows all users including soft deleted ones', function (): void {
    actingAsAdmin();

    User::factory()->create(['first_name' => 'Active', 'last_name' => 'User']);
    User::factory()->create(['first_name' => 'Deleted', 'last_name' => 'User'])->delete();

    get(route('users.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Users/Index')
            ->has('users.0.roles')
            ->has('users', User::withTrashed()->count())
            ->where('users.0.first_name', test()->user->first_name)
        );
});
