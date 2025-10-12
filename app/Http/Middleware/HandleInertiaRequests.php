<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Inertia\Middleware;
use Override;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function share(Request $request): array
    {
        $loggedIn = Auth::check();
        $user = Auth::user()?->loadMissing('settings');

        return [
            ...parent::share($request),
            'appName' => config('app.name'),
            'ziggy' => fn (): array => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'user' => $loggedIn
                ? $user
                : null,
            'connectorEnabled' => $loggedIn
                ? $user->connectors()->enabled()->exists()
                : false,
            'locale' => app()->getLocale(),
            'translations' => $this->getTranslations(),
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'success' => fn () => $request->session()->get('success'),
            ],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    protected function getTranslations(): array
    {
        $locale = app()->getLocale();
        $langPath = lang_path("{$locale}");

        if (! File::exists($langPath)) {
            return [];
        }

        $translations = [];
        $files = File::files($langPath);

        foreach ($files as $file) {
            $key = $file->getFilenameWithoutExtension();
            $translations[$key] = __($key);
        }

        return $translations;
    }
}
