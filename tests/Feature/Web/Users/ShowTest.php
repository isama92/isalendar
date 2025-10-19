<?php

use App\Enums\Role;
use App\Models\User;

use function Pest\Laravel\get;

uses()->group('web', 'users');

it('redirects unauthenticated users to login', function (): void {
    $user = User::factory()->create();

    get(route('users.show', $user))
        ->assertRedirect(route('login'));
});

it('prevents regular users from viewing user details', function (): void {
    actingAsUser();
    $otherUser = User::factory()->create();

    get(route('users.show', $otherUser))
        ->assertForbidden();
});

it('allows super admins to view user details', function (): void {
    actingAsAdmin();
    $user = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);

    get(route('users.show', $user))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Users/Show')
            ->has('user')
            ->where('user.id', $user->id)
            ->where('user.first_name', 'John')
            ->where('user.last_name', 'Doe')
            ->where('user.email', 'john.doe@example.com')
            ->has('user.roles')
            ->has('roles', 2)
            ->where('roles.0.value', Role::USER->value)
            ->where('roles.0.label', Role::USER->label())
            ->where('roles.1.value', Role::SUPER_ADMIN->value)
            ->where('roles.1.label', Role::SUPER_ADMIN->label())
        );
});
