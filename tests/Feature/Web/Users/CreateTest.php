<?php

use App\Enums\Role;

use function Pest\Laravel\get;

uses()->group('web', 'users');

it('redirects unauthenticated users to login', function (): void {
    get(route('users.create'))
        ->assertRedirect(route('login'));
});

it('prevents regular users from accessing user create page', function (): void {
    actingAsUser();

    get(route('users.create'))
        ->assertForbidden();
});

it('allows super admins to access user create page', function (): void {
    actingAsAdmin();

    get(route('users.create'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Users/Form')
            ->has('roles')
            ->missing('user')
        );
});

it('shows available roles', function (): void {
    actingAsAdmin();

    get(route('users.create'))
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
