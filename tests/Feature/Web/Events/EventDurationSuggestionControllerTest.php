<?php

use App\Models\User;

use function Pest\Laravel\get;
use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;
use function Tests\Helpers\eventFactory;

uses()->group('web', 'events');

it('returns duration suggestions based on similar event titles', function (): void {
    actingAsUser();

    // Create events with similar titles and varying durations
    eventFactory()->create([
        'title' => 'Team Meeting',
        'all_day' => false,
        'starts_at' => now()->setTime(9, 0),
        'ends_at' => now()->setTime(10, 0), // 60 minutes
    ]);

    eventFactory()->create([
        'title' => 'Team Meeting Review',
        'all_day' => false,
        'starts_at' => now()->setTime(14, 0),
        'ends_at' => now()->setTime(15, 0), // 60 minutes
    ]);

    eventFactory()->create([
        'title' => 'Team Meeting Planning',
        'all_day' => false,
        'starts_at' => now()->setTime(10, 0),
        'ends_at' => now()->setTime(11, 30), // 90 minutes
    ]);

    get(route('events.suggest-duration', ['title' => 'Team', 'all_day' => false]))
        ->assertSuccessful()
        ->assertJsonCount(2) // 60 minutes appears twice, 90 minutes once
        ->assertJson([60, 90]); // Most common first
});

it('returns empty array when no similar events are found', function (): void {
    actingAsUser();

    get(route('events.suggest-duration', ['title' => 'Nonexistent Event', 'all_day' => false]))
        ->assertSuccessful()
        ->assertJson([]);
});

it('filters by all_day flag', function (): void {
    actingAsUser();

    // Create timed event
    eventFactory()->create([
        'title' => 'Conference',
        'all_day' => false,
        'starts_at' => now()->setTime(9, 0),
        'ends_at' => now()->setTime(17, 0), // 480 minutes
    ]);

    // Create all-day event with same title
    eventFactory()->create([
        'title' => 'Conference Day',
        'all_day' => true,
        'starts_at' => now()->startOfDay(),
        'ends_at' => now()->addDay()->startOfDay(), // 1440 minutes
    ]);

    // Query for timed events
    get(route('events.suggest-duration', ['title' => 'Conference', 'all_day' => false]))
        ->assertSuccessful()
        ->assertJson([480]);

    // Query for all-day events
    get(route('events.suggest-duration', ['title' => 'Conference', 'all_day' => true]))
        ->assertSuccessful()
        ->assertJson([1440]);
});

it('returns maximum of 3 suggestions', function (): void {
    actingAsUser();

    // Create 5 events with different durations
    eventFactory()->create([
        'title' => 'Task',
        'all_day' => false,
        'starts_at' => now()->setTime(9, 0),
        'ends_at' => now()->setTime(9, 30), // 30 minutes
    ]);

    eventFactory()->create([
        'title' => 'Task',
        'all_day' => false,
        'starts_at' => now()->setTime(10, 0),
        'ends_at' => now()->setTime(11, 0), // 60 minutes
    ]);

    eventFactory()->create([
        'title' => 'Task',
        'all_day' => false,
        'starts_at' => now()->setTime(13, 0),
        'ends_at' => now()->setTime(14, 30), // 90 minutes
    ]);

    eventFactory()->create([
        'title' => 'Task',
        'all_day' => false,
        'starts_at' => now()->setTime(15, 0),
        'ends_at' => now()->setTime(17, 0), // 120 minutes
    ]);

    eventFactory()->create([
        'title' => 'Task',
        'all_day' => false,
        'starts_at' => now()->setTime(18, 0),
        'ends_at' => now()->setTime(20, 30), // 150 minutes
    ]);

    get(route('events.suggest-duration', ['title' => 'Task', 'all_day' => false]))
        ->assertSuccessful()
        ->assertJsonCount(3); // Maximum 3 suggestions
});

it('only shows suggestions for current user events', function (): void {
    actingAsUser();
    $anotherUser = User::factory()->create();

    // Create event for current user
    eventFactory()->create([
        'title' => 'My Meeting',
        'all_day' => false,
        'starts_at' => now()->setTime(9, 0),
        'ends_at' => now()->setTime(10, 0), // 60 minutes
    ]);

    // Create event for another user
    eventFactory()
        ->for(calendarFactory(connector: connectorFactory()->for($anotherUser)->create()))
        ->create([
            'title' => 'My Meeting',
            'all_day' => false,
            'starts_at' => now()->setTime(14, 0),
            'ends_at' => now()->setTime(16, 0), // 120 minutes
        ]);

    get(route('events.suggest-duration', ['title' => 'My Meeting', 'all_day' => false]))
        ->assertSuccessful()
        ->assertJson([60]); // Only user's own event duration
});

it('requires authentication', function (): void {
    get(route('events.suggest-duration', ['title' => 'Test', 'all_day' => false]))
        ->assertRedirect(route('login'));
});

it('requires title parameter', function (): void {
    actingAsUser();

    get(route('events.suggest-duration', ['all_day' => false]))
        ->assertStatus(302); // Validation redirect
});

it('requires title to be at least 2 characters', function (): void {
    actingAsUser();

    get(route('events.suggest-duration', ['title' => 'A', 'all_day' => false]))
        ->assertStatus(302); // Validation redirect
});

it('performs case-insensitive search', function (): void {
    actingAsUser();

    eventFactory()->create([
        'title' => 'Daily Standup',
        'all_day' => false,
        'starts_at' => now()->setTime(9, 0),
        'ends_at' => now()->setTime(9, 15), // 15 minutes
    ]);

    get(route('events.suggest-duration', ['title' => 'daily', 'all_day' => false]))
        ->assertSuccessful()
        ->assertJson([15]);

    get(route('events.suggest-duration', ['title' => 'DAILY', 'all_day' => false]))
        ->assertSuccessful()
        ->assertJson([15]);
});
