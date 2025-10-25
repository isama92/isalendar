<?php

use App\Actions\GetThrottleLimitKey;
use App\Models\User;
use App\Services\Calendar\GoogleCalendar\Actions\Synchronize as GoogleCalendarSynchronize;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Spatie\QueueableAction\Testing\QueueableActionFake;

use function Pest\Laravel\put;
use function Pest\Laravel\withoutMiddleware;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;

uses()->group('web', 'connectors');

beforeEach(fn () => RateLimiter::clear('connector-sync'));

it('synchronizes connector successfully', function (): void {
    Queue::fake();

    actingAsUser();

    $connector = connectorFactory()->create();
    calendarFactory(connector: $connector)->create();

    put(route('connectors.synchronize', $connector))
        ->assertRedirect(route('calendars.index'))
        ->assertSessionHas('success');

    QueueableActionFake::assertPushed(GoogleCalendarSynchronize::class);
});

it('prevents synchronization when connector is disabled', function (): void {
    Queue::fake();

    actingAsUser();

    $connector = connectorFactory()->disabled()->create();

    put(route('connectors.synchronize', $connector))
        ->assertNotFound();

    Queue::assertNothingPushed();
});

it('prevents synchronization when rate limited', function (): void {
    Queue::fake();

    $user = actingAsUser();

    $connector = connectorFactory()->create();

    $key = app(GetThrottleLimitKey::class)->execute('connector-sync', [$user->id, $connector->id]);
    RateLimiter::hit($key, 3600);

    put(route('connectors.synchronize', $connector))
        ->assertTooManyRequests();

    Queue::assertNothingPushed();
});

it('does not rate limit without the middleware', function (): void {
    Queue::fake();

    actingAsUser();

    $connector = connectorFactory()->create();
    calendarFactory(connector: $connector)->create();

    withoutMiddleware(ThrottleRequests::class)
        ->put(route('connectors.synchronize', $connector))
        ->assertRedirect(route('calendars.index'));

    withoutMiddleware(ThrottleRequests::class)
        ->put(route('connectors.synchronize', $connector))
        ->assertRedirect(route('calendars.index'));

    QueueableActionFake::assertPushedTimes(GoogleCalendarSynchronize::class, 2);
});

it('prevents synchronization of another user connector', function (): void {
    Queue::fake();

    $connector = connectorFactory()->for(User::factory())->create();

    actingAsUser();

    put(route('connectors.synchronize', $connector))
        ->assertForbidden();

    Queue::assertNothingPushed();
});

it('redirects unauthenticated users to login', function (): void {
    $connector = connectorFactory()->create();

    put(route('connectors.synchronize', $connector))
        ->assertRedirect(route('login'));
});

it('allows synchronization after rate limit period', function (): void {
    Queue::fake();

    $user = actingAsUser();

    $connector = connectorFactory()->create();
    calendarFactory(connector: $connector)->create();

    put(route('connectors.synchronize', $connector))
        ->assertRedirect(route('calendars.index'));
    put(route('connectors.synchronize', $connector))
        ->assertTooManyRequests();

    $key = app(GetThrottleLimitKey::class)->execute('connector-sync', [$user->id, $connector->id]);
    RateLimiter::clear($key);

    put(route('connectors.synchronize', $connector))
        ->assertRedirect(route('calendars.index'));

    QueueableActionFake::assertPushedTimes(GoogleCalendarSynchronize::class, 2);
});
