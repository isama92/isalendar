<?php

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('dashboard displays stats for authenticated user', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Dashboard')
        ->has('totalEvents')
        ->has('eventsToday')
        ->has('eventsTomorrow')
        ->has('eventsThisWeek')
        ->has('eventsNextWeek')
        ->has('averagePerDayOfWeek')
        ->where('averagePerDayOfWeek', fn ($value): bool => count($value) === 7)
    );
});

test('dashboard shows correct event counts', function (): void {
    $user = User::factory()->create();

    $today = Carbon::now()->startOfDay();
    $tomorrow = Carbon::now()->addDay()->startOfDay();
    $thisWeekStart = Carbon::now()->startOfWeek();
    $nextWeekStart = Carbon::now()->addWeek()->startOfWeek();

    // Create events for different time periods
    Event::factory()->for($user)->create(['starts_at' => $today->copy()->addHours(10)]); // Today
    Event::factory()->for($user)->create(['starts_at' => $tomorrow->copy()->addHours(10)]); // Tomorrow
    Event::factory()->for($user)->create(['starts_at' => $thisWeekStart->copy()->addDays(2)]); // This week
    Event::factory()->for($user)->create(['starts_at' => $nextWeekStart->copy()->addDays(2)]); // Next week

    $response = $this->actingAs($user)->get('/');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Dashboard')
        ->where('totalEvents', 4)
        ->where('eventsToday', 1)
        ->where('eventsTomorrow', 1)
        ->where('eventsThisWeek', fn ($value): bool => $value >= 1)
        ->where('eventsNextWeek', fn ($value): bool => $value >= 1)
    );
});

test('dashboard only shows authenticated user events', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Event::factory()->count(5)->for($user)->create();
    Event::factory()->count(3)->for($otherUser)->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Dashboard')
        ->where('totalEvents', 5)
    );
});

test('dashboard redirects unauthenticated users to login', function (): void {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});
