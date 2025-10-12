<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\post;

uses()->group('web', 'auth');

it('logs out authenticated user', function (): void {
    $user = User::factory()->create();

    actingAs($user);
    expect(auth()->check())->toBeTrue();

    post(route('logout'))
        ->assertRedirect(route('login'))
        ->assertSessionHasNoErrors();

    expect(auth()->check())->toBeFalse();
    assertGuest();
});

it('invalidates session on logout', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $sessionId = session()->getId();

    post(route('logout'))
        ->assertRedirect(route('login'));

    expect(session()->getId())->not->toBe($sessionId);
    assertGuest();
});

it('regenerates csrf token on logout', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $oldToken = csrf_token();

    post(route('logout'));

    expect(csrf_token())->not->toBe($oldToken);
});

it('requires authentication to logout', function (): void {
    post(route('logout'))
        ->assertRedirect(route('login'));
});
