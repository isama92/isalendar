<?php

use App\Models\User;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses()->group('web', 'profile');

beforeEach(function (): void {
    Storage::fake('profile_photos');
    test()->disk = Storage::disk('profile_photos');
});

it('displays user photo when it exists', function (): void {
    $user = actingAsUser();

    $content = 'fake-image-content';
    test()->disk->put($user->photo_path, $content);

    $response = get(route('profile.photo'))
        ->assertSuccessful()
        ->assertHeader('Content-Type', 'image/webp');

    expect($response->getContent())->toBe($content);
});

it('returns 404 when photo does not exist', function (): void {
    actingAsUser();

    get(route('profile.photo'))
        ->assertNotFound();
});

it('redirects unauthenticated users to login', function (): void {
    get(route('profile.photo'))
        ->assertRedirect(route('login'));
});

it('does not serve photos from other disks', function (): void {
    $user = actingAsUser();

    Storage::fake();
    Storage::put($user->photo_path, 'photo-content');

    get(route('profile.photo'))
        ->assertNotFound();
});

it('serves different photos for different users', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $content1 = 'user1-photo';
    $content2 = 'user2-photo';

    test()->disk->put($user1->photo_path, $content1);
    test()->disk->put($user2->photo_path, $content2);

    actingAs($user1);
    $response1 = get(route('profile.photo'));
    $response1->assertSuccessful();
    expect($response1->getContent())->toBe($content1);

    actingAs($user2);
    $response2 = get(route('profile.photo'));
    $response2->assertSuccessful();
    expect($response2->getContent())->toBe($content2);
});
