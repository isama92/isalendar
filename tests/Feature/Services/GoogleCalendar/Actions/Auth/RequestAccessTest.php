<?php

use App\Enums\Service;
use App\Services\Calendar\GoogleCalendar\Actions\Auth\RequestAccess;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Mockery\MockInterface;

uses()->group('gcalendar', 'gcalendar.auth');

it('builds the google oauth redirect with expected scopes, prompt and access_type', function (): void {
    // Capture what the action sends to the provider
    $capturedScopes = null;
    $capturedWith = null;

    /** @var GoogleProvider|MockInterface $provider */
    $provider = Mockery::mock(GoogleProvider::class);

    $provider->shouldReceive('scopes')
        ->once()
        ->with(Mockery::on(function ($scopes) use (&$capturedScopes): bool {
            $capturedScopes = $scopes;

            return $scopes === Service::GOOGLE_CALENDAR->scopes();
        }))
        ->andReturnSelf();

    $provider->shouldReceive('with')
        ->once()
        ->with(Mockery::on(function ($with) use (&$capturedWith): bool {
            $capturedWith = $with;

            return $with === Service::GOOGLE_CALENDAR->accessParams();
        }))
        ->andReturnSelf();

    // Have redirect() return a URL that includes the captured data so we can assert on it
    $provider->shouldReceive('redirect')
        ->once()
        ->andReturnUsing(function () use (&$capturedScopes, &$capturedWith): RedirectResponse {
            $query = http_build_query([
                'access_type' => $capturedWith['access_type'] ?? null,
                'prompt' => $capturedWith['prompt'] ?? null,
                // Socialite sends space-delimited scopes to Google
                'scope' => implode(' ', $capturedScopes ?? []),
            ]);

            return new RedirectResponse("https://accounts.google.com/o/oauth2/v2/auth?{$query}");
        });

    Socialite::shouldReceive('driver')
        ->once()
        ->with('google')
        ->andReturn($provider);

    // Act
    $response = app(RequestAccess::class)->execute();

    // Assert

    $url = $response->getTargetUrl();
    $parts = parse_url($url);
    parse_str($parts['query'] ?? '', $query);

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($query['access_type'] ?? null)->toBe('offline')
        ->and($query['prompt'] ?? null)->toBe('consent')
        ->and($query['scope'] ?? null)->toBe(implode(' ', Service::GOOGLE_CALENDAR->scopes()));
});
