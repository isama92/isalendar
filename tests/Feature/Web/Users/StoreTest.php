<?php

use App\Enums\Role;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

uses()->group('web', 'users');

it('creates user successfully', function (): void {
    actingAsAdmin();

    post(route('users.store'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'password' => 'password123',
        'role' => Role::USER->value,
    ])
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success');

    assertDatabaseHas('users', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);

    $user = User::where('email', 'john.doe@example.com')->sole();
    expect($user->hasRole(Role::USER))->toBeTrue();
});

it('creates user without password and generates random one', function (): void {
    actingAsAdmin();

    post(route('users.store'), [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane.doe@example.com',
        'role' => Role::USER->value,
    ])
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success');

    assertDatabaseHas('users', [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane.doe@example.com',
    ]);

    $user = User::where('email', 'jane.doe@example.com')->sole();
    expect($user->password)->not->toBeEmpty();
});

it('redirects to new when redirect_to is new', function (): void {
    actingAsAdmin();

    post(route('users.store'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'role' => Role::USER->value,
        'redirect_to' => 'new',
    ])
        ->assertRedirect(route('users.create'));
});

it('redirects to show when redirect_to is show', function (): void {
    actingAsAdmin();

    $response = post(route('users.store'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'role' => Role::USER->value,
        'redirect_to' => 'show',
    ]);

    $user = User::where('email', 'john.doe@example.com')->sole();

    $response->assertRedirect(route('users.show', $user));
});

it('redirects unauthenticated users to login', function (): void {
    post(route('users.store'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'role' => Role::USER->value,
    ])
        ->assertRedirect(route('login'));
});

it('prevents regular users from creating users', function (): void {
    actingAsUser();

    post(route('users.store'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'role' => Role::USER->value,
    ])
        ->assertForbidden();
});

describe('Validation', function (): void {
    it('required fields are required', function (): void {
        actingAsAdmin();

        post(route('users.store'), [])
            ->assertSessionHasErrors([
                'first_name',
                'last_name',
                'email',
                'role',
            ])
            ->assertSessionDoesntHaveErrors([
                'password',
            ]);
    });

    it('validates first_name must be string', function (): void {
        actingAsAdmin();

        post(route('users.store'), [
            'first_name' => 12345,
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'role' => Role::USER->value,
        ])
            ->assertSessionHasErrors('first_name');
    });

    it('validates first_name must not exceed 255 characters', function (): void {
        actingAsAdmin();

        post(route('users.store'), [
            'first_name' => str_repeat('a', 256),
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'role' => Role::USER->value,
        ])
            ->assertSessionHasErrors('first_name');
    });

    it('validates last_name must be string', function (): void {
        actingAsAdmin();

        post(route('users.store'), [
            'first_name' => 'John',
            'last_name' => 12345,
            'email' => 'john.doe@example.com',
            'role' => Role::USER->value,
        ])
            ->assertSessionHasErrors('last_name');
    });

    it('validates last_name must not exceed 255 characters', function (): void {
        actingAsAdmin();

        post(route('users.store'), [
            'first_name' => 'John',
            'last_name' => str_repeat('a', 256),
            'email' => 'john.doe@example.com',
            'role' => Role::USER->value,
        ])
            ->assertSessionHasErrors('last_name');
    });

    it('validates email must be valid email', function (): void {
        actingAsAdmin();

        post(route('users.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'invalid-email',
            'role' => Role::USER->value,
        ])
            ->assertSessionHasErrors('email');
    });

    it('validates email must not exceed 255 characters', function (): void {
        actingAsAdmin();

        post(route('users.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => str_repeat('a', 256).'@example.com',
            'role' => Role::USER->value,
        ])
            ->assertSessionHasErrors('email');
    });

    it('validates email must be unique', function (): void {
        actingAsAdmin();
        User::factory()->create(['email' => 'existing@example.com']);

        post(route('users.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@example.com',
            'role' => Role::USER->value,
        ])
            ->assertSessionHasErrors('email');
    });

    it('validates password must be at least 8 characters', function (): void {
        actingAsAdmin();

        post(route('users.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'short',
            'role' => Role::USER->value,
        ])
            ->assertSessionHasErrors('password');
    });

    it('validates password must be string', function (): void {
        actingAsAdmin();

        post(route('users.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 12345678,
            'role' => Role::USER->value,
        ])
            ->assertSessionHasErrors('password');
    });

    it('allows null password', function (): void {
        actingAsAdmin();

        post(route('users.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => null,
            'role' => Role::USER->value,
        ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    });

    it('validates role must exist in roles table', function (): void {
        actingAsAdmin();

        post(route('users.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'role' => 'invalid_role',
        ])
            ->assertSessionHasErrors('role');
    });

    it('validates role must be string', function (): void {
        actingAsAdmin();

        post(route('users.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'role' => 12345,
        ])
            ->assertSessionHasErrors('role');
    });
});
