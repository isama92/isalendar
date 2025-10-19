<?php

use App\Models\User;

use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\delete;

uses()->group('web', 'users');

it('allows super admin to soft delete user', function (): void {
    actingAsAdmin();
    $user = User::factory()->create();

    delete(route('users.destroy', $user))
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success');

    assertSoftDeleted('users', ['id' => $user->id]);
});

it('allows super admin to restore soft deleted user', function (): void {
    actingAsAdmin();
    $user = User::factory()->create();
    $user->delete();

    delete(route('users.destroy', $user))
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success');

    assertNotSoftDeleted('users', ['id' => $user->id]);
});

it('allows super admin to delete themselves', function (): void {
    $admin = actingAsAdmin();

    delete(route('users.destroy', $admin))
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success');

    assertSoftDeleted('users', ['id' => $admin->id]);
});

it('allows super admin to restore themselves', function (): void {
    $admin = actingAsAdmin();
    $admin->delete();

    delete(route('users.destroy', $admin))
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success');

    assertNotSoftDeleted('users', ['id' => $admin->id]);
});

it('prevents unauthenticated user from deleting user', function (): void {
    $user = User::factory()->create();

    delete(route('users.destroy', $user))
        ->assertRedirect(route('login'));

    assertNotSoftDeleted('users', ['id' => $user->id]);
});

it('prevents regular users from deleting other users', function (): void {
    actingAsUser();
    $otherUser = User::factory()->create();

    delete(route('users.destroy', $otherUser))
        ->assertForbidden();

    assertNotSoftDeleted('users', ['id' => $otherUser->id]);
});

it('prevents regular users from restoring soft deleted users', function (): void {
    actingAsUser();
    $otherUser = User::factory()->create();
    $otherUser->delete();

    delete(route('users.destroy', $otherUser))
        ->assertForbidden();

    assertSoftDeleted('users', ['id' => $otherUser->id]);
});
