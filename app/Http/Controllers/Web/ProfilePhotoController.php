<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ProfilePhotoController extends Controller
{
    public function show(): HttpResponse
    {
        $user = Auth::user();
        $disk = Storage::disk('profile_photos');

        if (! $disk->exists($user->photo_path)) {
            abort(SymfonyResponse::HTTP_NOT_FOUND);
        }

        return response($disk->get($user->photo_path), SymfonyResponse::HTTP_OK)
            ->header('Content-Type', $disk->mimeType($user->photo_path));
    }
}
