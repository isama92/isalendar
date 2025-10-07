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
     * @var string
     */
    protected $rootView = 'app';

    #[Override]
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'ziggy' => fn (): array => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'user' => Auth::check()
                ? Auth::user()->only(['name', 'email'])
                : null,
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
            $translations[$key] = require $file->getPathname();
        }

        return $translations;
    }
}
