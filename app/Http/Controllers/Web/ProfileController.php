<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Profile/Edit');
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        Auth::user()->update($request->validated());

        return redirect()->route('profile.edit')
            ->with('success', __('profile.profile_updated_successfully'));
    }
}
