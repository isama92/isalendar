<?php

use App\Models\User;
use App\Services\Auth\Oidc\OidcProvider;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as OAuth2User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

uses()->group('auth');

describe('oidc connect', function (): void {
    it('redirects to oidc oauth2 url', function (): void {
        $oidcProvider = Mockery::mock(OidcProvider::class);
        $oidcProvider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect('https://oidc.example.com/oauth/authorize'));

        Socialite::shouldReceive('driver')
            ->once()
            ->with('oidc')
            ->andReturn($oidcProvider);

        get(route('auth.oidc.connect'))
            ->assertRedirect('https://oidc.example.com/oauth/authorize');
    });
});

describe('oidc redirect', function (): void {
    it('creates or updates user and logs them in', function (): void {
        User::factory()->create([
            'email' => 'test@example.com',
            'first_name' => 'Old',
            'last_name' => 'Name',
        ]);

        $socialiteUser = Mockery::mock(OAuth2User::class);
        $socialiteUser->shouldReceive('getEmail')->andReturn('test@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('John Doe');

        $oidcProvider = Mockery::mock(OidcProvider::class);
        $oidcProvider->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('oidc')
            ->andReturn($oidcProvider);

        get(route('auth.oidc.redirect'))
            ->assertRedirect('/');

        assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        expect(Auth::check())->toBeTrue()
            ->and(Auth::user()->email)->toBe('test@example.com');
    });

    it('updates existing user when user exists', function (): void {
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
            'first_name' => 'Old',
            'last_name' => 'Name',
        ]);

        $socialiteUser = Mockery::mock(OAuth2User::class);
        $socialiteUser->shouldReceive('getEmail')->andReturn('existing@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Updated User');

        $oidcProvider = Mockery::mock(OidcProvider::class);
        $oidcProvider->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('oidc')
            ->andReturn($oidcProvider);

        get(route('auth.oidc.redirect'))
            ->assertRedirect('/');

        $existingUser->refresh();

        expect(Auth::check())->toBeTrue()
            ->and(Auth::user()->is($existingUser))->toBeTrue()
            ->and($existingUser)
            ->first_name->toBe('Updated')
            ->last_name->toBe('User');
    });

    it('logs in the user with remember me enabled', function (): void {
        User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $socialiteUser = Mockery::mock(OAuth2User::class);
        $socialiteUser->shouldReceive('getEmail')->andReturn('user@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Jane Smith');

        $oidcProvider = Mockery::mock(OidcProvider::class);
        $oidcProvider->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('oidc')
            ->andReturn($oidcProvider);

        get(route('auth.oidc.redirect'))
            ->assertRedirect('/');

        expect(Auth::check())->toBeTrue()
            ->and(Auth::viaRemember())->toBeFalse();
    });

    it('redirects to dashboard after successful authentication', function (): void {
        User::factory()->create([
            'email' => 'redirect@example.com',
        ]);

        $socialiteUser = Mockery::mock(OAuth2User::class);
        $socialiteUser->shouldReceive('getEmail')->andReturn('redirect@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Bob Wilson');

        $oidcProvider = Mockery::mock(OidcProvider::class);
        $oidcProvider->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('oidc')
            ->andReturn($oidcProvider);

        get(route('auth.oidc.redirect'))
            ->assertRedirect('/');
    });

    it('handles single name correctly', function (): void {
        $user = User::factory()->create([
            'email' => 'singlename@example.com',
        ]);

        $socialiteUser = Mockery::mock(OAuth2User::class);
        $socialiteUser->shouldReceive('getEmail')->andReturn('singlename@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Maria');

        $oidcProvider = Mockery::mock(OidcProvider::class);
        $oidcProvider->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('oidc')
            ->andReturn($oidcProvider);

        get(route('auth.oidc.redirect'))
            ->assertRedirect('/');

        $user->refresh();

        expect($user)
            ->first_name->toBe('Maria')
            ->last_name->toBe('');
    });

    it('prevents soft-deleted users from logging in', function (): void {
        $user = User::factory()->create([
            'email' => 'deleted@example.com',
        ]);

        $user->delete();

        $socialiteUser = Mockery::mock(OAuth2User::class);
        $socialiteUser->shouldReceive('getEmail')->andReturn('deleted@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Deleted User');

        $oidcProvider = Mockery::mock(OidcProvider::class);
        $oidcProvider->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('oidc')
            ->andReturn($oidcProvider);

        get(route('auth.oidc.redirect'))
            ->assertForbidden();

        expect(Auth::check())->toBeFalse();
    });
});
