<?php

use App\Enums\Role;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses()->group('web', 'setup');

beforeEach(function (): void {
    test()->user->settings->forceDelete();
    test()->user->forceDelete();
});

describe('setup create', function (): void {
    it('displays setup page when no users exist', function (): void {
        get(route('setup'))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Setup')
                ->has('appName')
            );
    });
});

describe('setup store', function (): void {
    it('creates first user with super admin role', function (): void {
        post(route('setup.submit'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHasNoErrors();

        assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'admin@example.com',
        ]);

        $user = User::where('email', 'admin@example.com')->sole();
        expect($user->hasRole(Role::SUPER_ADMIN))->toBeTrue();
    });

    it('requires fields', function (): void {
        post(route('setup.submit'))
            ->assertSessionHasErrors([
                'first_name',
                'last_name',
                'email',
                'password',
            ]);
    });

    it('validates email format', function (): void {
        post(route('setup.submit'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'not-an-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertSessionHasErrors('email');
    });

    it('validates email is unique', function (): void {
        post(route('setup.submit'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'first@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHasNoErrors();

        post(route('setup.submit'), [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'first@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertRedirect(route('login'));
    });

    it('requires password to be at least 8 characters', function (): void {
        post(route('setup.submit'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'admin@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])
            ->assertSessionHasErrors('password');
    });

    it('requires password confirmation to match', function (): void {
        post(route('setup.submit'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ])
            ->assertSessionHasErrors('password');
    });

    it('redirects to login when users already exist', function (): void {
        User::factory()->create();

        post(route('setup.submit'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertRedirect(route('login'));
    });
});

describe('redirect middleware', function (): void {
    it('redirects all routes to setup when no users exist', function (): void {
        get(route('login'))
            ->assertRedirect(route('setup'));

        get(route('dashboard'))
            ->assertRedirect(route('setup'));
    });

    it('allows normal access when users exist', function (): void {
        User::factory()->create();

        get(route('login'))
            ->assertSuccessful();
    });

    it('redirects to login when users exist', function (): void {
        User::factory()->create();

        get(route('setup'))
            ->assertRedirect(route('login'));
    });

    it('redirects to login when only soft deleted users exist', function (): void {
        User::factory()->create()->delete();

        get(route('setup'))
            ->assertRedirect(route('login'));
    });
});
