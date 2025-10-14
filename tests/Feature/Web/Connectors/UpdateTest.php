<?php

use App\Enums\Service;
use App\Models\Connector;
use App\Models\Provider;
use App\Models\User;
use App\Services\GoogleCalendar\Actions\Synchronize as GoogleCalendarSynchronize;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Spatie\QueueableAction\Testing\QueueableActionFake;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;
use function Pest\Laravel\travel;
use function Pest\Laravel\withoutMiddleware;

uses()->group('web', 'connectors');

beforeEach(fn () => RateLimiter::clear('connector-sync'));

it('synchronizes connector successfully', function (): void {
    Queue::fake();

    $user = User::factory()->create();
    $connector = Connector::factory()
        ->for($user)
        ->for(Provider::googleCalendar())
        ->create();

    actingAs($user);

    put(route('connectors.synchronize', $connector))
        ->assertRedirect(route('calendars.index'))
        ->assertSessionHas('success');

    QueueableActionFake::assertPushed(GoogleCalendarSynchronize::class);
});

it('prevents synchronization when connector is disabled', function (): void {
    Queue::fake();

    $user = User::factory()->create();
    $connector = Connector::factory()
        ->for($user)
        ->for(Provider::googleCalendar())
        ->disabled()
        ->create();

    actingAs($user);

    put(route('connectors.synchronize', $connector))
        ->assertNotFound();

    Queue::assertNothingPushed();
});

it('prevents synchronization when rate limited', function (): void {
    Queue::fake();

    $user = User::factory()->create();
    $connector = Connector::factory()
        ->for($user)
        ->for(Provider::googleCalendar())
        ->create();

    actingAs($user);

    put(route('connectors.synchronize', $connector))
        ->assertRedirect(route('calendars.index'));
    put(route('connectors.synchronize', $connector))
        ->assertTooManyRequests();

    QueueableActionFake::assertPushedTimes(GoogleCalendarSynchronize::class, 1);
});

it('does not rate limit without the middleware', function (): void {
    Queue::fake();

    $user = User::factory()->create();
    $connector = Connector::factory()
        ->for($user)
        ->for(Provider::googleCalendar())
        ->create();

    actingAs($user);

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

    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $connector = Connector::factory()
        ->for($otherUser)
        ->for(Provider::googleCalendar())
        ->create();

    actingAs($user);

    put(route('connectors.synchronize', $connector))
        ->assertForbidden();

    Queue::assertNothingPushed();
});

it('redirects unauthenticated users to login', function (): void {
    $connector = Connector::factory()->create([
        'provider_id' => Service::GOOGLE_CALENDAR->id(),
        'enabled' => true,
    ]);

    put(route('connectors.synchronize', $connector))
        ->assertRedirect(route('login'));
});

it('allows synchronization after rate limit period', function (): void {
    Queue::fake();

    $user = User::factory()->create();
    $connector = Connector::factory()
        ->for($user)
        ->for(Provider::googleCalendar())
        ->create();

    actingAs($user);

    put(route('connectors.synchronize', $connector))
        ->assertRedirect(route('calendars.index'));
    put(route('connectors.synchronize', $connector))
        ->assertTooManyRequests();

    travel(61)->minutes();

    put(route('connectors.synchronize', $connector))
        ->assertRedirect(route('calendars.index'));

    QueueableActionFake::assertPushedTimes(GoogleCalendarSynchronize::class, 2);
});
