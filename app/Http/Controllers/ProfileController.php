<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(): Response
    {
        $user = Auth::user();

        return Inertia::render('Profile/Edit', [
            'hasGoogleCalendar' => $user->calendar()->exists(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        Auth::user()->update($request->validated());

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}
