<?php

use App\Actions\SaveUserPhoto;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\partialMock;
use function Pest\Laravel\put;

uses()->group('web', 'profile');

describe('profile update', function (): void {
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
});

describe('password update', function (): void {
    it('updates user password successfully', function (): void {
        $user = User::factory()->create([
            'password' => 'old-password',
        ]);

        actingAs($user);

        put(route('profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'default_timezone' => 'UTC',
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('success');

        $user->refresh();
        expect(Hash::check('new-password', $user->password))->toBeTrue()
            ->and(Hash::check('old-password', $user->password))->toBeFalse();
    });

    it('allows updating profile without changing password', function (): void {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'password' => 'old-password',
        ]);

        actingAs($user);

        put(route('profile.update'), [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'default_timezone' => 'America/Chicago',
        ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('success');

        expect($user->fresh())
            ->first_name->toBe('Jane')
            ->last_name->toBe('Smith')
            ->and(Hash::check('old-password', $user->password))->toBeTrue();
    });
});

describe('validation', function (): void {
    it('requires first_name field', function (): void {
        $user = User::factory()->create();

        actingAs($user);

        put(route('profile.update'), [])
            ->assertSessionHasErrors([
                'first_name',
                'last_name',
                'default_timezone',
            ]);
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

    it('requires current_password when changing password', function (): void {
        $user = User::factory()->create([
            'password' => 'old-password',
        ]);

        actingAs($user);

        put(route('profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'default_timezone' => 'UTC',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertSessionHasErrors('current_password');
    });

    it('validates current_password matches user password', function (): void {
        $user = User::factory()->create([
            'password' => 'old-password',
        ]);

        actingAs($user);

        put(route('profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'default_timezone' => 'UTC',
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertSessionHasErrors('current_password');
    });

    it('validates password minimum length 8', function (): void {
        $user = User::factory()->create([
            'password' => 'old-password',
        ]);

        actingAs($user);

        put(route('profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'default_timezone' => 'UTC',
            'current_password' => 'old-password',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])
            ->assertSessionHasErrors('password');
    });

    it('validates password is confirmed', function (): void {
        $user = User::factory()->create([
            'password' => 'old-password',
        ]);

        actingAs($user);

        put(route('profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'default_timezone' => 'UTC',
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ])
            ->assertSessionHasErrors('password');
    });
});
