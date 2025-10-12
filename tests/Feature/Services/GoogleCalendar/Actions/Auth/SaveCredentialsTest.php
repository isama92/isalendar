<?php

use App\Enums\Service;
use App\Models\User;
use App\Services\GoogleCalendar\Actions\Auth\SaveCredentials;
use App\Services\GoogleCalendar\Auth\AccessToken;
use Laravel\Socialite\Two\User as GoogleUser;
use Mockery\MockInterface;

uses()->group('gcalendar', 'gcalendar-auth');

it('saves credentials and creates a disabled Google Calendar connector with access token', function (): void {
    $user = User::factory()->create();

    $googleId = 'google-123';
    $token = 'access_xyz';
    $refresh = 'refresh_abc';
    $expiresIn = 3600;

    /** @var GoogleUser|MockInterface $providerUser */
    $providerUser = Mockery::mock(GoogleUser::class);

    // getId() is called twice (external_id and AccessToken id)
    $providerUser->shouldReceive('getId')
        ->twice()
        ->andReturn($googleId);

    // Socialite's GoogleUser exposes these as public properties
    $providerUser->token = $token;
    $providerUser->refreshToken = $refresh;
    $providerUser->expiresIn = $expiresIn;

    $connector = app(SaveCredentials::class)->execute($user, $providerUser);

    expect($connector->refresh())
        ->user->toBeInstanceOf(User::class)
        ->user->is($user)->toBeTrue()
        ->external_id->toBe($googleId)
        ->provider_id->toBe(Service::GOOGLE_CALENDAR->id())
        ->enabled->toBeFalse()
        ->and($connector->access_details)
        ->toBeInstanceOf(AccessToken::class)
        ->id->toBe($googleId)
        ->token->toBe($token)
        ->refreshToken->toBe($refresh)
        ->expiresIn->toBe($expiresIn);
});
