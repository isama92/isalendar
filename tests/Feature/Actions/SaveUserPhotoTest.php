<?php

use App\Actions\SaveUserPhoto;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses()->group('actions');

it('saves user photo as webp to profile_photos disk', function (): void {
    Storage::fake('profile_photos');

    $user = User::factory()->create();
    $photo = UploadedFile::fake()->image('profile.jpg', 500, 500);

    app(SaveUserPhoto::class)->execute($user, $photo);

    Storage::disk('profile_photos')->assertExists($user->photo_path);
});

it('deletes old photo before saving new one', function (): void {
    Storage::fake('profile_photos');

    $user = User::factory()->create();
    $oldPhoto = UploadedFile::fake()->image('old.jpg');
    $newPhoto = UploadedFile::fake()->image('new.jpg');

    // Save first photo
    app(SaveUserPhoto::class)->execute($user, $oldPhoto);

    expect(Storage::disk('profile_photos')->exists($user->photo_path))->toBeTrue();

    // Save second photo (should delete the first)
    app(SaveUserPhoto::class)->execute($user, $newPhoto);

    // Only one file should exist (the new one)
    expect(Storage::disk('profile_photos')->exists($user->photo_path))->toBeTrue()
        ->and(Storage::disk('profile_photos')->allFiles())->toHaveCount(1);
});

it('handles large images by resizing to 128px width', function (): void {
    Storage::fake('profile_photos');

    $user = User::factory()->create();
    $photo = UploadedFile::fake()->image('large.jpg', 2000, 2000);

    app(SaveUserPhoto::class)->execute($user, $photo);

    expect(Storage::disk('profile_photos')->exists($user->photo_path))->toBeTrue();

    $savedImage = Storage::disk('profile_photos')->get($user->photo_path);
    $imageSize = getimagesizefromstring($savedImage);

    expect($imageSize[0])->toBe(128); // Width should be 128px
});

it('converts uploaded photo to webp format', function (): void {
    Storage::fake('profile_photos');

    $user = User::factory()->create();
    $photo = UploadedFile::fake()->image('photo.png'); // PNG input

    app(SaveUserPhoto::class)->execute($user, $photo);

    expect(Storage::disk('profile_photos')->exists($user->photo_path))->toBeTrue();

    $savedImage = Storage::disk('profile_photos')->get($user->photo_path);
    $imageInfo = getimagesizefromstring($savedImage);

    expect($imageInfo['mime'])->toBe('image/webp');
});
