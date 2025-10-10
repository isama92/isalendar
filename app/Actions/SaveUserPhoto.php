<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;

class SaveUserPhoto
{
    public function execute(User $user, UploadedFile $photo): void
    {
        $disk = Storage::disk('profile_photos');

        $disk->delete($user->photo_path);

        $manager = new ImageManager(new GdDriver);

        $encoded = $manager->read($photo->getRealPath())
            ->scale(width: 128)
            ->toWebp();

        $disk->put($user->photo_path, (string) $encoded);
    }
}
