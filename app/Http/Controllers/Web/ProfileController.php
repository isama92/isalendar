<?php

namespace App\Http\Controllers\Web;

use App\Actions\SaveUserPhoto;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Profile/Edit');
    }

    public function update(UpdateProfileRequest $request, SaveUserPhoto $saveUserPhoto): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            $saveUserPhoto->execute($user, $request->file('photo'));
        }

        $user->update(Arr::except($validated, 'photo'));

        return redirect()->route('profile.edit')
            ->with('success', __('profile.profile_updated_successfully'));
    }
}
