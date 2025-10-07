<?php

namespace App\Services\GoogleCalendar\Actions;

use App\Models\Calendar;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Cache;

readonly class GetAuthClient
{
    public function __construct(private GoogleClient $googleClient) {}

    public function execute(Calendar $calendar): GoogleClient
    {
        $this->googleClient->setAccessToken($calendar->external_token);

        if (! $this->googleClient->isAccessTokenExpired()) {
            return $this->googleClient;
        }

        return Cache::lock(sprintf('calendar-refresh-%s', $calendar->id), 10)
            ->block(5, function () use ($calendar): GoogleClient {
                $this->googleClient->fetchAccessTokenWithRefreshToken($calendar->external_refresh_token);
                $newToken = $this->googleClient->getAccessToken();
                $calendar->update([
                    'external_token' => $newToken['access_token'],
                    'external_token_expires_at' => now()->addSeconds($newToken['expires_in']),
                ]);

                return $this->googleClient;
            });
    }
}
