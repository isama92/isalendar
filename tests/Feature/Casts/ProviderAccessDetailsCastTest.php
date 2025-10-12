<?php

use App\Models\Connector;
use App\Services\GoogleCalendar\Auth\AccessToken;
use Illuminate\Support\Facades\Crypt;

uses()->group('casts');

it('encrypts and stores access token when setting', function (): void {
    $accessToken = new AccessToken(
        id: 'token-123',
        token: 'access-token',
        refreshToken: 'refresh-token',
        expiresIn: 3600
    );

    $connector = Connector::factory()->create([
        'access_details' => $accessToken,
    ]);

    $rawValue = $connector->getAttributes()['access_details'];
    $decrypted = Crypt::decryptString($rawValue);
    $unserialized = unserialize($decrypted);

    expect($rawValue)
        ->not->toBeNull()
        ->not->toBe($accessToken)
        ->and(is_string($rawValue))->toBeTrue()
        ->and($unserialized)
        ->toBeInstanceOf(AccessToken::class)
        ->id->toBe('token-123')
        ->and($connector->refresh()->access_details)
        ->toBeInstanceOf(AccessToken::class)
        ->id->toBe('token-123')
        ->token->toBe('access-token')
        ->refreshToken->toBe('refresh-token')
        ->expiresIn->toBe(3600);
});

it('decrypts and returns access token when getting', function (): void {
    $accessToken = new AccessToken(
        id: 'token-456',
        token: 'access-abc',
        refreshToken: 'refresh-xyz',
        expiresIn: 7200
    );

    $connector = Connector::factory()->create([
        'access_details' => $accessToken,
    ]);

    $retrieved = $connector->access_details;

    expect($retrieved)
        ->toBeInstanceOf(AccessToken::class)
        ->id->toBe('token-456')
        ->token->toBe('access-abc')
        ->refreshToken->toBe('refresh-xyz')
        ->expiresIn->toBe(7200);
});

it('returns null when cast receives null value during get', function (): void {
    $connector = Connector::factory()->make();
    $connector->setRawAttributes(['access_details' => null]);

    expect($connector->access_details)->toBeNull();
});

it('updates access token', function (): void {
    $oldToken = new AccessToken(
        id: 'old-id',
        token: 'old-token',
        refreshToken: 'old-refresh',
        expiresIn: 3600
    );

    $connector = Connector::factory()->create([
        'access_details' => $oldToken,
    ]);

    $newToken = new AccessToken(
        id: 'new-id',
        token: 'new-token',
        refreshToken: 'new-refresh',
        expiresIn: 7200
    );

    $connector->update(['access_details' => $newToken]);

    expect(Connector::sole()->access_details)
        ->toBeInstanceOf(AccessToken::class)
        ->id->toBe('new-id')
        ->token->toBe('new-token')
        ->refreshToken->toBe('new-refresh')
        ->expiresIn->toBe(7200);
});
