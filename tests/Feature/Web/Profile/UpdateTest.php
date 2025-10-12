<?php

use App\Actions\SaveUserPhoto;
use App\Models\User;
use Illuminate\Http\UploadedFile;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\partialMock;
use function Pest\Laravel\put;

uses()->group('web', 'profile');

it('updates user profile successfully', function (): void {
    $user = User::factory()->create([
        'first_name' => 'OldFN',
        'last_name' => 'OldLN',
    ]);

    partialMock(SaveUserPhoto::class)
        ->shouldNotReceive('execute');

    actingAs($user);

    put(route('profile.update'), [
        'first_name' => 'NewFN',
        'last_name' => 'NewLN',
        'default_timezone' => 'America/New_York',
    ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success');

    expect($user->fresh())
        ->first_name->toBe('NewFN')
        ->last_name->toBe('NewLN')
        ->and($user->settings)
        ->not->toBeNull()
        ->default_timezone->toBe('America/New_York');

});

it('uploads user photo', function (): void {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg');

    partialMock(SaveUserPhoto::class)
        ->shouldReceive('execute')
        ->once()
        ->with($user, $file);

    actingAs($user);

    put(route('profile.update'), [
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'default_timezone' => 'UTC',
        'photo' => $file,
    ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success');
});

it('redirects unauthenticated users to login', function (): void {
    put(route('profile.update'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'default_timezone' => 'UTC',
    ])
        ->assertRedirect(route('login'));
});

it('requires first_name field', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    put(route('profile.update'), [])
        ->assertSessionHasErrors('first_name')
        ->assertSessionHasErrors('last_name')
        ->assertSessionHasErrors('default_timezone');
});

it('validates first_name is string', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    put(route('profile.update'), [
        'first_name' => 12345,
        'last_name' => 'Doe',
        'default_timezone' => 'UTC',
    ])
        ->assertSessionHasErrors('first_name');
});

it('validates last_name is string', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    put(route('profile.update'), [
        'first_name' => 'John',
        'last_name' => 12345,
        'default_timezone' => 'UTC',
    ])
        ->assertSessionHasErrors('last_name');
});

it('validates first_name max length 255', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    put(route('profile.update'), [
        'first_name' => str_repeat('a', 256),
        'last_name' => 'Doe',
        'default_timezone' => 'UTC',
    ])
        ->assertSessionHasErrors('first_name');
});

it('validates last_name max length 255', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    put(route('profile.update'), [
        'first_name' => 'John',
        'last_name' => str_repeat('a', 256),
        'default_timezone' => 'UTC',
    ])
        ->assertSessionHasErrors('last_name');
});

it('validates timezone is valid', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    put(route('profile.update'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'default_timezone' => 'Invalid/Timezone',
    ])
        ->assertSessionHasErrors('default_timezone');
});

it('validates photo mime type', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    put(route('profile.update'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'default_timezone' => 'UTC',
        'photo' => UploadedFile::fake()->create('document.pdf'),
    ])
        ->assertSessionHasErrors('photo');
});

it('validates photo max size 2MB', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    put(route('profile.update'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'default_timezone' => 'UTC',
        'photo' => UploadedFile::fake()->image('photo.jpg')->size(2049),
    ])
        ->assertSessionHasErrors('photo');
});

it('updates only specified fields', function (): void {
    $user = User::factory()->create([
        'first_name' => 'Original',
        'last_name' => 'Name',
        'email' => 'original@example.com',
    ]);

    actingAs($user);

    put(route('profile.update'), [
        'first_name' => 'Updated',
        'last_name' => 'Name',
        'default_timezone' => 'UTC',
        'email' => 'new@example.com',
    ]);

    expect($user->fresh())
        ->first_name->toBe('Updated')
        ->email->toBe('original@example.com');
});
