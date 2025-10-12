<?php

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses()->group('web', 'profile');

it('displays profile edit page', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $timezoneGroupsCount = collect(timezone_identifiers_list())
        ->map(fn (string $tz) => Str::before($tz, '/'))
        ->unique()
        ->count();

    get(route('profile.edit'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Profile/Edit')
            ->has('timezones')
            ->where('timezones', fn (Collection $timezones): bool => $timezones->count() === $timezoneGroupsCount)
            ->has('timezones.0.label')
            ->has('timezones.0.options')
            ->has('timezones.0.options.0.value')
            ->has('timezones.0.options.0.label')
        );
});

it('redirects unauthenticated users to login', function (): void {
    get(route('profile.edit'))
        ->assertRedirect(route('login'));
});
