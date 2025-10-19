<?php

use App\Enums\Role;
use App\Models\User;

use function Pest\Laravel\put;

uses()->group('web', 'users');

it('updates user successfully', function (): void {
    actingAsAdmin();
    $user = User::factory()->create([
        'first_name' => 'Old First',
        'last_name' => 'Old Last',
        'email' => 'old@example.com',
    ]);

    put(route('users.update', $user), [
        'first_name' => 'New First',
        'last_name' => 'New Last',
        'email' => 'new@example.com',
        'role' => Role::USER->value,
    ])
        ->assertRedirect(route('users.show', $user))
        ->assertSessionHas('success');

    expect($user->fresh())
        ->first_name->toBe('New First')
        ->last_name->toBe('New Last')
        ->email->toBe('new@example.com');
});

it('updates user password when provided', function (): void {
    actingAsAdmin();
    $user = User::factory()->create();
    $oldPassword = $user->password;

    put(route('users.update', $user), [
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'email' => $user->email,
        'password' => 'newpassword123',
        'role' => Role::USER->value,
    ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($user->fresh()->password)
        ->not->toBe($oldPassword);
});

it('does not update password when not provided', function (): void {
    actingAsAdmin();
    $user = User::factory()->create();
    $oldPassword = $user->password;

    put(route('users.update', $user), [
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'email' => $user->email,
        'role' => Role::USER->value,
    ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($user->fresh()->password)->toBe($oldPassword);
});

it('does not update password when empty string provided', function (): void {
    actingAsAdmin();
    $user = User::factory()->create();
    $oldPassword = $user->password;

    put(route('users.update', $user), [
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'email' => $user->email,
        'password' => '',
        'role' => Role::USER->value,
    ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($user->fresh()->password)->toBe($oldPassword);
});

it('updates user role', function (): void {
    actingAsAdmin();
    $user = User::factory()->create();
    $user->assignRole(Role::USER);

    put(route('users.update', $user), [
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'email' => $user->email,
        'role' => Role::SUPER_ADMIN->value,
    ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($user->fresh())
        ->hasRole(Role::SUPER_ADMIN)->toBeTrue()
        ->hasRole(Role::USER)->toBeFalse();
});

it('redirects to edit when redirect_to is edit', function (): void {
    actingAsAdmin();
    $user = User::factory()->create();

    put(route('users.update', $user), [
        'first_name' => 'Updated',
        'last_name' => 'Name',
        'email' => $user->email,
        'role' => Role::USER->value,
        'redirect_to' => 'edit',
    ])
        ->assertRedirect(route('users.edit', $user));
});

it('redirects to index when redirect_to is index', function (): void {
    actingAsAdmin();
    $user = User::factory()->create();

    put(route('users.update', $user), [
        'first_name' => 'Updated',
        'last_name' => 'Name',
        'email' => $user->email,
        'role' => Role::USER->value,
        'redirect_to' => 'index',
    ])
        ->assertRedirect(route('users.index'));
});

it('redirects unauthenticated users to login', function (): void {
    $user = User::factory()->create();

    put(route('users.update', $user), [
        'first_name' => 'Updated',
        'last_name' => 'Name',
        'email' => $user->email,
        'role' => Role::USER->value,
    ])
        ->assertRedirect(route('login'));
});

it('prevents regular users from updating users', function (): void {
    actingAsUser();
    $otherUser = User::factory()->create(['first_name' => 'Original']);

    put(route('users.update', $otherUser), [
        'first_name' => 'Hacked',
        'last_name' => $otherUser->last_name,
        'email' => $otherUser->email,
        'role' => Role::USER->value,
    ])
        ->assertForbidden();

    expect($otherUser->fresh()->first_name)->toBe('Original');
});

it('returns 404 for soft deleted users', function (): void {
    actingAsAdmin();
    $user = User::factory()->create(['first_name' => 'Original']);
    $user->delete();

    put(route('users.update', $user), [
        'first_name' => 'Updated',
        'last_name' => $user->last_name,
        'email' => $user->email,
        'role' => Role::USER->value,
    ])
        ->assertNotFound();

    expect(User::withTrashed()->find($user->id)->first_name)->toBe('Original');
});

describe('Validation', function (): void {
    it('required fields are required', function (): void {
        actingAsAdmin();
        $user = User::factory()->create();

        put(route('users.update', $user), [])
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
        $user = User::factory()->create();

        put(route('users.update', $user), [
            'first_name' => 12345,
            'last_name' => 'Doe',
            'email' => $user->email,
            'role' => Role::USER->value,
        ])
            ->assertSessionHasErrors('first_name');
    });

    it('validates first_name must not exceed 255 characters', function (): void {
        actingAsAdmin();
        $user = User::factory()->create();

        put(route('users.update', $user), [
            'first_name' => str_repeat('a', 256),
            'last_name' => 'Doe',
            'email' => $user->email,
            'role' => Role::USER->value,
        ])
            ->assertSessionHasErrors('first_name');
    });

    it('validates last_name must be string', function (): void {
        actingAsAdmin();
        $user = User::factory()->create();

        put(route('users.update', $user), [
            'first_name' => 'John',
            'last_name' => 12345,
            'email' => $user->email,
            'role' => Role::USER->value,
        ])
            ->assertSessionHasErrors('last_name');
    });

    it('validates last_name must not exceed 255 characters', function (): void {
        actingAsAdmin();
        $user = User::factory()->create();

        put(route('users.update', $user), [
            'first_name' => 'John',
            'last_name' => str_repeat('a', 256),
            'email' => $user->email,
            'role' => Role::USER->value,
        ])
            ->assertSessionHasErrors('last_name');
    });

    it('validates email must be valid email', function (): void {
        actingAsAdmin();
        $user = User::factory()->create();

        put(route('users.update', $user), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'invalid-email',
            'role' => Role::USER->value,
        ])
            ->assertSessionHasErrors('email');
    });

    it('validates email must not exceed 255 characters', function (): void {
        actingAsAdmin();
        $user = User::factory()->create();

        put(route('users.update', $user), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => str_repeat('a', 256).'@example.com',
            'role' => Role::USER->value,
        ])
            ->assertSessionHasErrors('email');
    });

    it('validates email must be unique except for current user', function (): void {
        actingAsAdmin();
        User::factory()->create(['email' => 'existing@example.com']);
        $user = User::factory()->create(['email' => 'user@example.com']);

        put(route('users.update', $user), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@example.com',
            'role' => Role::USER->value,
        ])
            ->assertSessionHasErrors('email');
    });

    it('allows keeping same email when updating', function (): void {
        actingAsAdmin();
        $user = User::factory()->create(['email' => 'user@example.com']);

        put(route('users.update', $user), [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => 'user@example.com',
            'role' => Role::USER->value,
        ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    });

    it('validates password must be at least 8 characters', function (): void {
        actingAsAdmin();
        $user = User::factory()->create();

        put(route('users.update', $user), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'password' => 'short',
            'role' => Role::USER->value,
        ])
            ->assertSessionHasErrors('password');
    });

    it('validates password must be string', function (): void {
        actingAsAdmin();
        $user = User::factory()->create();

        put(route('users.update', $user), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'password' => 12345678,
            'role' => Role::USER->value,
        ])
            ->assertSessionHasErrors('password');
    });

    it('allows null password', function (): void {
        actingAsAdmin();
        $user = User::factory()->create();

        put(route('users.update', $user), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'password' => null,
            'role' => Role::USER->value,
        ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    });

    it('validates role must exist in roles table', function (): void {
        actingAsAdmin();
        $user = User::factory()->create();

        put(route('users.update', $user), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'role' => 'invalid_role',
        ])
            ->assertSessionHasErrors('role');
    });

    it('validates role must be string', function (): void {
        actingAsAdmin();
        $user = User::factory()->create();

        put(route('users.update', $user), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'role' => 12345,
        ])
            ->assertSessionHasErrors('role');
    });
});
