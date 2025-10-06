<?php

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

use function Pest\Laravel\actingAs;

it('redirects to google oauth', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('services.google.connect'))
        ->assertRedirect();
});

it('stores calendar connection after oauth callback', function (): void {
    $user = User::factory()->create();

    $socialiteUser = new SocialiteUser;
    $socialiteUser->id = 'google-user-id-123';
    $socialiteUser->token = 'access-token-123';
    $socialiteUser->refreshToken = 'refresh-token-123';
    $socialiteUser->expiresIn = 3600;

    Socialite::shouldReceive('driver->user')
        ->once()
        ->andReturn($socialiteUser);

    actingAs($user)
        ->get(route('services.google.redirect'))
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('calendars', [
        'user_id' => $user->id,
        'external_user_id' => 'google-user-id-123',
        'external_calendar_id' => 'primary',
        'external_token' => 'access-token-123',
        'external_refresh_token' => 'refresh-token-123',
    ]);
});

it('disconnects calendar connection', function (): void {
    $user = User::factory()->create();

    $user->calendar()->create([
        'external_user_id' => 'google-user-id-123',
        'external_calendar_id' => 'primary',
        'external_token' => 'access-token-123',
        'external_refresh_token' => 'refresh-token-123',
        'external_token_expires_at' => now()->addHour(),
    ]);

    actingAs($user)
        ->post(route('services.google.disconnect'))
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('calendars', [
        'user_id' => $user->id,
    ]);
});

it('shows google calendar status on profile page', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('profile.edit'))
        ->assertInertia(fn ($page) => $page
            ->component('Profile/Edit')
            ->has('user')
            ->where('hasGoogleCalendar', false)
        );

    $user->calendar()->create([
        'external_user_id' => 'google-user-id-123',
        'external_calendar_id' => 'primary',
        'external_token' => 'access-token-123',
        'external_refresh_token' => 'refresh-token-123',
        'external_token_expires_at' => now()->addHour(),
    ]);

    $user->refresh();

    actingAs($user)
        ->get(route('profile.edit'))
        ->assertInertia(fn ($page) => $page
            ->component('Profile/Edit')
            ->has('user')
            ->where('hasGoogleCalendar', true)
        );
});
