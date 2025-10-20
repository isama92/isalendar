<?php

use Illuminate\Support\Collection;

use function Pest\Laravel\get;

uses()->group('web', 'profile');

it('displays profile edit page', function (): void {
    actingAsUser();

    get(route('profile.edit'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Profile/Edit')
            ->has('timezones')
            ->where('timezones', fn (Collection $timezones): bool => $timezones
                ->count() === count(timezone_identifiers_list()))
            ->has('timezones.0.value')
            ->has('timezones.0.label')
            ->where('timezones.0.label', '(-11:00) Pacific/Midway')
        );
});

it('redirects unauthenticated users to login', function (): void {
    get(route('profile.edit'))
        ->assertRedirect(route('login'));
});
