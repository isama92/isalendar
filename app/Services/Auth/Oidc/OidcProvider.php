<?php

namespace App\Services\Auth\Oidc;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User as OAuth2User;

class OidcProvider extends AbstractProvider
{
    /**
     * @var array<int, string>
     */
    protected $scopes = ['openid', 'profile', 'email'];

    protected $scopeSeparator = ' ';

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase(
            config('services.oidc.authorize_url'),
            $state
        );
    }

    protected function getTokenUrl(): string
    {
        return config('services.oidc.token_url');
    }

    /**
     * @return array<string, mixed>
     */
    protected function getUserByToken($token): array
    {
        $response = $this->getHttpClient()->get(
            config('services.oidc.userinfo_url'),
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                ],
            ]
        );

        return json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param  array<string, mixed>  $user
     */
    protected function mapUserToObject(array $user): OAuth2User
    {
        return (new OAuth2User)->setRaw($user)->map([
            'id' => $user['sub'] ?? null,
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $user['picture'] ?? null,
        ]);
    }
}
