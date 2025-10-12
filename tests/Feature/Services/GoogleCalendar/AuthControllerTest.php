<?php

use App\Models\Connector;
use App\Models\User;
use App\Services\GoogleCalendar\Actions\Auth\RequestAccess;
use App\Services\GoogleCalendar\Actions\Auth\SaveCredentials;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as GoogleUser;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\mock;
use function Pest\Laravel\partialMock;

uses()->group('gcalendar', 'gcalendar.auth');

describe('google auth create', function (): void {
    it('redirects users to google oauth2 url', function (): void {
        partialMock(RequestAccess::class)
            ->shouldReceive('execute')
            ->andReturn(new RedirectResponse('https://example.com/redirect-url'));

        actingAs(User::factory()->create());

        get(route('services.google.auth.connect'))
            ->assertRedirect('https://example.com/redirect-url');
    });

    it('redirects unauthenticated users to login', function (): void {
        get(route('services.google.auth.connect'))
            ->assertRedirect(route('login'));
    });
});

describe('google auth store', function (): void {
    it('saves credentials and redirects to calendars create page', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $googleUser = Mockery::mock(GoogleUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-123');
        $googleUser->token = 'access_token';
        $googleUser->refreshToken = 'refresh_token';
        $googleUser->expiresIn = 3600;

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturnSelf();

        Socialite::shouldReceive('user')
            ->once()
            ->andReturn($googleUser);

        mock(SaveCredentials::class)
            ->shouldReceive('execute')
            ->once()
            ->with($user, $googleUser)
            ->andReturn($connector);

        actingAs($user);

        get(route('services.google.auth.redirect'))
            ->assertRedirect(route('calendars.create', $connector->getRouteKey()));
    });

    it('passes authenticated user to SaveCredentials action', function (): void {
        $user = User::factory()->create();
        $connector = Connector::factory()->for($user)->create();

        $googleUser = Mockery::mock(GoogleUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-456');
        $googleUser->token = 'token_xyz';
        $googleUser->refreshToken = 'refresh_xyz';
        $googleUser->expiresIn = 7200;

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturnSelf();

        Socialite::shouldReceive('user')
            ->once()
            ->andReturn($googleUser);

        mock(SaveCredentials::class)
            ->shouldReceive('execute')
            ->once()
            ->withArgs(fn (User $actualUser, GoogleUser $actualGoogleUser): bool => $actualUser->is($user) &&
                    $actualGoogleUser === $googleUser)
            ->andReturn($connector);

        actingAs($user);

        get(route('services.google.auth.redirect'));
    });

    it('redirects unauthenticated users to login', function (): void {
        get(route('services.google.auth.redirect'))
            ->assertRedirect(route('login'));
    });

    it('aborts with 403 when Socialite throws InvalidStateException', function (): void {
        $user = User::factory()->create();

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturnSelf();

        Socialite::shouldReceive('user')
            ->once()
            ->andThrow(new InvalidStateException);

        actingAs($user);

        get(route('services.google.auth.redirect'))
            ->assertForbidden();
    });
});
