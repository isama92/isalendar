<?php

use App\Models\Connector;
use App\Services\GoogleCalendar\Actions\Auth\GetAuthClient;
use App\Services\GoogleCalendar\Auth\AccessToken;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Cache;
use Mockery\MockInterface;
use Tests\Helpers\ImmediateLock;

use function Pest\Laravel\mock;

uses()->group('gcalendar', 'gcalendar.auth');

it('returns the google client without refreshing when the token is not expired', function (): void {
    $token = new AccessToken(
        id: 'tok_123',
        token: 'access_xyz',
        refreshToken: 'refresh_abc',
        expiresIn: 3600
    );

    $connector = Connector::factory()->create([
        'access_details' => $token,
    ]);

    Cache::partialMock()
        ->shouldReceive('lock')
        ->never();

    mock(GoogleClient::class, function (MockInterface $mock) use ($token): void {
        $mock->shouldReceive('setAccessToken')
            ->once()
            ->with($token->token);

        $mock->shouldReceive('isAccessTokenExpired')
            ->once()
            ->andReturnFalse();

        $mock->shouldNotReceive('fetchAccessTokenWithRefreshToken');
        $mock->shouldNotReceive('getAccessToken');
    });

    $client = app(GetAuthClient::class)->execute($connector);

    expect($client)->toBeInstanceOf(GoogleClient::class)
        ->and($connector->fresh()->access_details)->toEqual($token);
});

it('refreshes the token via cache lock when the token is expired and persists the new details', function (): void {
    $oldToken = new AccessToken(
        id: 'tok_123',
        token: 'access_old',
        refreshToken: 'refresh_old',
        expiresIn: 3600
    );

    $newTokenPayload = [
        'access_token' => 'access_new',
        'refresh_token' => 'refresh_new',
        'expires_in' => 7200,
    ];

    $connector = Connector::factory()->create([
        'access_details' => $oldToken,
    ]);

    Cache::partialMock()
        ->shouldReceive('lock')
        ->once()
        ->with(sprintf('connector-refresh-%s', $connector->id), 10)
        ->andReturn(new ImmediateLock);

    mock(GoogleClient::class, function (MockInterface $mock) use ($oldToken, $newTokenPayload): void {
        $mock->shouldReceive('setAccessToken')
            ->once()
            ->with($oldToken->token);

        $mock->shouldReceive('isAccessTokenExpired')
            ->once()
            ->andReturnTrue();

        $mock->shouldReceive('fetchAccessTokenWithRefreshToken')
            ->once()
            ->with($oldToken->refreshToken);

        $mock->shouldReceive('getAccessToken')
            ->once()
            ->andReturn($newTokenPayload);
    });

    $client = app(GetAuthClient::class)->execute($connector);

    expect($client)->toBeInstanceOf(GoogleClient::class)
        ->and($connector->fresh()->access_details)
        ->toBeInstanceOf(AccessToken::class)
        ->id->toBe($oldToken->id)
        ->token->toBe($newTokenPayload['access_token'])
        ->refreshToken->toBe($newTokenPayload['refresh_token'])
        ->expiresIn->toBe($newTokenPayload['expires_in']);
});

it('calls refresh exactly once even if executed concurrently thanks to the cache lock', function (): void {
    $oldToken = new AccessToken(
        id: 'tok_123',
        token: 'access_old',
        refreshToken: 'refresh_old',
        expiresIn: 3600
    );

    $newGooglePayload = [
        'access_token' => 'access_new',
        'refresh_token' => 'refresh_new',
        'expires_in' => 7200,
    ];

    $connector = Connector::factory()->create([
        'access_details' => $oldToken,
    ]);

    Cache::partialMock()
        ->shouldReceive('lock')
        ->once()
        ->with(sprintf('connector-refresh-%s', $connector->id), 10)
        ->andReturn(new ImmediateLock);

    mock(GoogleClient::class, function (MockInterface $mock) use ($newGooglePayload): void {
        // 1st execute(): old token, expired -> refresh
        $mock->shouldReceive('setAccessToken')->once()->with('access_old')->ordered();
        $mock->shouldReceive('isAccessTokenExpired')->once()->andReturnTrue()->ordered();
        $mock->shouldReceive('fetchAccessTokenWithRefreshToken')->once()->with('refresh_old')->ordered();
        $mock->shouldReceive('getAccessToken')->once()->andReturn($newGooglePayload)->ordered();

        // 2nd execute(): new token, NOT expired -> no refresh
        $mock->shouldReceive('setAccessToken')->once()->with('access_new')->ordered();
        $mock->shouldReceive('isAccessTokenExpired')->once()->andReturnFalse()->ordered();

        // No second refresh calls should occur
        $mock->shouldNotReceive('fetchAccessTokenWithRefreshToken');
        $mock->shouldNotReceive('getAccessToken');
    });

    // Execute twice to simulate concurrent callers
    app(GetAuthClient::class)->execute($connector);
    app(GetAuthClient::class)->execute($connector);

    expect(Connector::sole()->access_details)
        ->token->toBe('access_new')
        ->refreshToken->toBe('refresh_new');
});
