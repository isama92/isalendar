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

uses()->group('web', 'calendars');

beforeEach(fn () => RateLimiter::clear('ui-calendar-sync'));

it('synchronizes calendar successfully', function (): void {
    Queue::fake();

    actingAsUser();

    $calendar = calendarFactory()->create();

    put(route('calendars.synchronize', $calendar))
        ->assertRedirect(route('calendars.index'))
        ->assertSessionHas('success');

    QueueableActionFake::assertPushed(GoogleCalendarSynchronize::class);
});

it('prevents synchronization when calendar is disabled', function (): void {
    Queue::fake();

    actingAsUser();

    $calendar = calendarFactory()->disabled()->create();

    put(route('calendars.synchronize', $calendar))
        ->assertNotFound();

    Queue::assertNothingPushed();
});

it('prevents synchronization when rate limited', function (): void {
    Queue::fake();

    $user = actingAsUser();

    $calendar = calendarFactory()->create();

    $key = app(GetThrottleLimitKey::class)->execute('ui-calendar-sync', [$user->id, $calendar->id]);
    RateLimiter::hit($key, 3600);

    put(route('calendars.synchronize', $calendar))
        ->assertTooManyRequests();

    Queue::assertNothingPushed();
});

it('does not rate limit without the middleware', function (): void {
    Queue::fake();

    actingAsUser();

    $calendar = calendarFactory()->create();

    withoutMiddleware(ThrottleRequests::class)
        ->put(route('calendars.synchronize', $calendar))
        ->assertRedirect(route('calendars.index'));

    withoutMiddleware(ThrottleRequests::class)
        ->put(route('calendars.synchronize', $calendar))
        ->assertRedirect(route('calendars.index'));

    QueueableActionFake::assertPushedTimes(GoogleCalendarSynchronize::class, 2);
});

it('prevents synchronization of another user calendar', function (): void {
    Queue::fake();

    $otherUserConnector = connectorFactory()->for(User::factory())->create();
    $calendar = calendarFactory(connector: $otherUserConnector)->create();

    actingAsUser();

    put(route('calendars.synchronize', $calendar))
        ->assertForbidden();

    Queue::assertNothingPushed();
});

it('redirects unauthenticated users to login', function (): void {
    $calendar = calendarFactory()->create();

    put(route('calendars.synchronize', $calendar))
        ->assertRedirect(route('login'));
});

it('allows synchronization after rate limit period', function (): void {
    Queue::fake();

    $user = actingAsUser();

    $calendar = calendarFactory()->create();

    put(route('calendars.synchronize', $calendar))
        ->assertRedirect(route('calendars.index'));
    put(route('calendars.synchronize', $calendar))
        ->assertTooManyRequests();

    $key = app(GetThrottleLimitKey::class)->execute('ui-calendar-sync', [$user->id, $calendar->id]);
    RateLimiter::clear($key);

    put(route('calendars.synchronize', $calendar))
        ->assertRedirect(route('calendars.index'));

    QueueableActionFake::assertPushedTimes(GoogleCalendarSynchronize::class, 2);
});
