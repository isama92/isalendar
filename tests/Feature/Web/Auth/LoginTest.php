<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses()->group('web', 'auth');

describe('login create', function (): void {
    it('displays login page', function (): void {
        get(route('login'))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Auth/Login')
            );
    });
});

describe('login store', function (): void {
    it('authenticates user with valid credentials', function (): void {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        post(route('login.submit'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasNoErrors();

        assertAuthenticated();
    });

    it('regenerates session on successful login', function (): void {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = post(route('login.submit'), [
            'email' => 'user@example.com',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasNoErrors();
        assertAuthenticated();
    });

    it('redirects to intended url after login', function (): void {
        $user = User::factory()->create([
            'email' => 'intended@example.com',
            'password' => bcrypt('password123'),
        ]);

        session()->put('url.intended', route('calendars.index'));

        post(route('login.submit'), [
            'email' => 'intended@example.com',
            'password' => 'password123',
        ])
            ->assertRedirect(route('calendars.index'));

        assertAuthenticated();
    });

    it('fails with invalid credentials', function (): void {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('correct-password'),
        ]);

        post(route('login.submit'), [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ])
            ->assertSessionHasErrors('email');

        assertGuest();
    });

    it('fails with non-existent email', function (): void {
        post(route('login.submit'), [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ])
            ->assertSessionHasErrors('email');

        assertGuest();
    });

    it('requires email field', function (): void {
        post(route('login.submit'), [
            'password' => 'password123',
        ])
            ->assertSessionHasErrors('email');

        assertGuest();
    });

    it('requires password field', function (): void {
        post(route('login.submit'), [
            'email' => 'test@example.com',
        ])
            ->assertSessionHasErrors('password');

        assertGuest();
    });

    it('validates email format', function (): void {
        post(route('login.submit'), [
            'email' => 'not-an-email',
            'password' => 'password123',
        ])
            ->assertSessionHasErrors('email');

        assertGuest();
    });

    it('redirects already authenticated users to dashboard', function (): void {
        $user = User::factory()->create();

        actingAs($user);

        post(route('login.submit'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ])
            ->assertRedirect(route('dashboard'));
    });

    it('trims whitespace from email', function (): void {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        post(route('login.submit'), [
            'email' => '  test@example.com  ',
            'password' => 'password123',
        ])
            ->assertRedirect(route('dashboard'));

        assertAuthenticated();
    });
});
