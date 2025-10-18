<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\Oidc\OidcProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as OAuth2User;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class OidcController extends Controller
{
    public function create(): SymfonyResponse
    {
        return ensure(Socialite::driver('oidc'), OidcProvider::class)
            ->redirect();
    }

    public function store(): RedirectResponse
    {
        $oidcProvider = ensure(Socialite::driver('oidc'), OidcProvider::class);
        $oidcUser = ensure($oidcProvider->user(), OAuth2User::class);

        $user = User::query()
            ->withTrashed()
            ->where('email', $oidcUser->getEmail())
            ->first();

        if ($user?->trashed()) {
            abort(SymfonyResponse::HTTP_FORBIDDEN, __('auth.account_deactivated'));
        }

        $user = $this->createOrUpdateUser($oidcUser, $user);

        Auth::login($user, remember: true);

        return redirect()->intended('/');
    }

    private function createOrUpdateUser(OAuth2User $oidcUser, ?User $user = null): User
    {
        $nameParts = explode(' ', (string) $oidcUser->getName(), 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        if ($user instanceof User) {
            $user->update([
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]);

            return $user;
        }

        return User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $oidcUser->getEmail(),
        ]);
    }
}
