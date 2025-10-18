<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses()->group('web', 'auth');

describe('password reset link create', function (): void {
    it('displays forgot password page', function (): void {
        get(route('password.request'))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Auth/ForgotPassword')
            );
    });

    it('redirects authenticated users to dashboard', function (): void {
        $user = User::factory()->create();

        actingAs($user);

        get(route('password.request'))
            ->assertRedirect(route('dashboard'));
    });
});

describe('password reset link store', function (): void {
    it('sends reset link to existing user', function (): void {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        post(route('password.email'), [
            'email' => 'user@example.com',
        ])
            ->assertRedirect()
            ->assertSessionHas('success');

        Notification::assertSentTo($user, ResetPassword::class);
    });

    it('does not reveal non-existent email addresses', function (): void {
        Notification::fake();

        post(route('password.email'), [
            'email' => 'nonexistent@example.com',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        Notification::assertNothingSent();
    });

    it('requires email field', function (): void {
        post(route('password.email'), [])
            ->assertSessionHasErrors('email');
    });

    it('validates email format', function (): void {
        post(route('password.email'), [
            'email' => 'not-an-email',
        ])
            ->assertSessionHasErrors('email');
    });

    it('trims whitespace from email', function (): void {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        post(route('password.email'), [
            'email' => '  user@example.com  ',
        ])
            ->assertRedirect()
            ->assertSessionHas('success');

        Notification::assertSentTo($user, ResetPassword::class);
    });

    it('throttles password reset requests', function (): void {
        Notification::fake();

        User::factory()->create([
            'email' => 'throttle@example.com',
        ]);

        // First request should succeed
        post(route('password.email'), [
            'email' => 'throttle@example.com',
        ])
            ->assertRedirect()
            ->assertSessionHas('success');

        // Immediate second request should be throttled
        post(route('password.email'), [
            'email' => 'throttle@example.com',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');
    });

    it('redirects authenticated users to dashboard', function (): void {
        $user = User::factory()->create();

        actingAs($user);

        post(route('password.email'), [
            'email' => 'test@example.com',
        ])
            ->assertRedirect(route('dashboard'));
    });
});

describe('password reset create', function (): void {
    it('displays reset password page with token and email', function (): void {
        $token = 'sample-reset-token';

        get(route('password.reset', ['token' => $token]).'?email=test@example.com')
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Auth/ResetPassword')
                ->has('token')
                ->where('token', $token)
                ->has('email')
                ->where('email', 'test@example.com')
            );
    });

    it('displays reset password page without email parameter', function (): void {
        $token = 'sample-reset-token';

        get(route('password.reset', ['token' => $token]))
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Auth/ResetPassword')
                ->has('token')
                ->where('token', $token)
                ->where('email', null)
            );
    });

    it('redirects authenticated users to dashboard', function (): void {
        $user = User::factory()->create();

        actingAs($user);

        get(route('password.reset', ['token' => 'some-token']))
            ->assertRedirect(route('dashboard'));
    });
});

describe('password reset store', function (): void {
    it('resets password with valid token', function (): void {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $token = Password::createToken($user);

        post(route('password.update'), [
            'token' => $token,
            'email' => 'user@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('success');

        expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
    });

    it('updates remember token on password reset', function (): void {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $oldRememberToken = $user->remember_token;
        $token = Password::createToken($user);

        post(route('password.update'), [
            'token' => $token,
            'email' => 'user@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('success');

        expect($user->refresh())
            ->remember_token->not->toBeNull()
            ->remember_token->not->toBe($oldRememberToken);
    });

    it('fails with invalid token', function (): void {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('old-password'),
        ]);

        post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => 'user@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        expect(Hash::check('old-password', $user->refresh()->password))->toBeTrue();
    });

    it('fails with expired token', function (): void {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $token = Password::createToken($user);

        // Travel past token expiration (default is 60 minutes)
        $this->travel(61)->minutes();

        post(route('password.update'), [
            'token' => $token,
            'email' => 'user@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        expect(Hash::check('old-password', $user->refresh()->password))->toBeTrue();
    });

    it('fails with non-existent email', function (): void {
        post(route('password.update'), [
            'token' => 'some-token',
            'email' => 'nonexistent@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');
    });

    it('requires token field', function (): void {
        post(route('password.update'), [
            'email' => 'user@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertSessionHasErrors('token');
    });

    it('requires email field', function (): void {
        post(route('password.update'), [
            'token' => 'some-token',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertSessionHasErrors('email');
    });

    it('validates email format', function (): void {
        post(route('password.update'), [
            'token' => 'some-token',
            'email' => 'not-an-email',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertSessionHasErrors('email');
    });

    it('requires password field', function (): void {
        post(route('password.update'), [
            'token' => 'some-token',
            'email' => 'user@example.com',
            'password_confirmation' => 'new-password',
        ])
            ->assertSessionHasErrors('password');
    });

    it('validates password minimum length', function (): void {
        post(route('password.update'), [
            'token' => 'some-token',
            'email' => 'user@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])
            ->assertSessionHasErrors('password');
    });

    it('validates password confirmation matches', function (): void {
        post(route('password.update'), [
            'token' => 'some-token',
            'email' => 'user@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ])
            ->assertSessionHasErrors('password');
    });

    it('fails when user is soft deleted', function (): void {
        $user = User::factory()->create([
            'email' => 'deleted@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $token = Password::createToken($user);

        $user->delete();

        post(route('password.update'), [
            'token' => $token,
            'email' => 'deleted@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');
    });

    it('redirects authenticated users to dashboard', function (): void {
        $user = User::factory()->create();

        actingAs($user);

        post(route('password.update'), [
            'token' => 'some-token',
            'email' => 'user@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertRedirect(route('dashboard'));
    });
});
