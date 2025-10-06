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
        $user = Auth::user();

        return Inertia::render('Profile/Edit', [
            'calendar' => $user->calendar?->only([
                'external_calendar_name',
                'external_calendar_description',
                'external_calendar_timezone',
            ]),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        Auth::user()->update($request->validated());

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}
