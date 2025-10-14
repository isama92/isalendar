<?php

namespace App\Services\GoogleCalendar\Actions\Auth;

use App\Models\Connector;
use App\Services\GoogleCalendar\Auth\AccessToken;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Cache;

class GetAuthClient
{
    public function __construct(private readonly GoogleClient $googleClient) {}

    public function execute(Connector $connector): GoogleClient
    {
        $accessToken = ensure($connector->access_details, AccessToken::class);
        $this->googleClient->setAccessToken($accessToken->token);

        if (! $this->googleClient->isAccessTokenExpired()) {
            return $this->googleClient;
        }

        // Refresh access token
        return Cache::lock(sprintf('connector-refresh-%d', $connector->id), 10)
            ->block(5, function () use ($connector, $accessToken): GoogleClient {
                $this->googleClient->fetchAccessTokenWithRefreshToken($accessToken->refreshToken);
                $newToken = $this->googleClient->getAccessToken();
                $connector->update([
                    'access_details' => new AccessToken(
                        $accessToken->id,
                        $newToken['access_token'],
                        $newToken['refresh_token'],
                        $newToken['expires_in'],
                    ),
                ]);

                return $this->googleClient;
            });
    }
}
