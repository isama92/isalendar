<?php

namespace App\Http\Controllers\Web;

use App\Actions\SaveUserPhoto;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Profile/Edit', [
            'timezones' => collect(timezone_identifiers_list())
                ->groupBy(fn ($tz) => Str::before($tz, '/'))
                ->map(fn (Collection $zones, string $label): array => [
                    'label' => $label,
                    'options' => $zones
                        ->sort()
                        ->values() // ensure numeric keys (JSON -> array, not object)
                        ->map(fn (string $tz): array => ['value' => $tz, 'label' => $tz])
                        ->all(),
                ])
                ->sortBy('label')
                ->values(),
        ]);
    }

    public function update(UpdateProfileRequest $request, SaveUserPhoto $saveUserPhoto): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            $saveUserPhoto->execute($user, $request->file('photo'));
        }

        $dataToUpdate = Arr::except($validated, [
            'photo',
            'default_timezone',
            'current_password',
            'password_confirmation',
        ]);

        if (empty($dataToUpdate['password'])) {
            unset($dataToUpdate['password']);
        }

        $user->update($dataToUpdate);

        $user->settings()->update(['default_timezone' => $validated['default_timezone']]);

        return redirect()->route('profile.edit')
            ->with('success', __('profile.profile_updated_successfully'));
    }
}
