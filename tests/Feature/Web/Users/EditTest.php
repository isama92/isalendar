<?php

use App\Enums\Role;
use App\Models\User;

use function Pest\Laravel\get;

uses()->group('web', 'users');

it('redirects unauthenticated users to login', function (): void {
    $user = User::factory()->create();

    get(route('users.edit', $user))
        ->assertRedirect(route('login'));
});

it('prevents regular users from accessing user edit page', function (): void {
    actingAsUser();
    $otherUser = User::factory()->create();

    get(route('users.edit', $otherUser))
        ->assertForbidden();
});

it('displays edit page with user data for super admins', function (): void {
    actingAsAdmin();
    $user = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);

    get(route('users.edit', $user))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Users/Form')
            ->has('user')
            ->where('user.id', $user->id)
            ->where('user.first_name', 'John')
            ->where('user.last_name', 'Doe')
            ->where('user.email', 'john.doe@example.com')
            ->has('user.roles')
            ->has('roles')
        );
});

it('shows user with their roles', function (): void {
    actingAsAdmin();
    $user = User::factory()->create();

    get(route('users.edit', $user))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Users/Form')
            ->has('user.roles')
        );
});

it('shows available roles', function (): void {
    actingAsAdmin();
    $user = User::factory()->create();

    get(route('users.edit', $user))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Users/Form')
            ->has('roles', 2)
            ->where('roles.0.value', Role::USER->value)
            ->where('roles.0.label', Role::USER->label())
            ->where('roles.1.value', Role::SUPER_ADMIN->value)
            ->where('roles.1.label', Role::SUPER_ADMIN->label())
        );
});

it('returns 404 for soft deleted users', function (): void {
    actingAsAdmin();
    $user = User::factory()->create();
    $user->delete();

    get(route('users.edit', $user))
        ->assertNotFound();
});
