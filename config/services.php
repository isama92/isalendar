<?php

return [
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('APP_URL').'/services/google/auth/redirect',
    ],

    'oidc' => [
        'enabled' => env('OIDC_ENABLED', false),
        'client_id' => env('OIDC_CLIENT_ID'),
        'client_secret' => env('OIDC_CLIENT_SECRET'),
        'redirect' => env('APP_URL').'/auth/oidc/redirect',
        'authorize_url' => env('OIDC_AUTHORIZE_URL'),
        'token_url' => env('OIDC_TOKEN_URL'),
        'userinfo_url' => env('OIDC_USERINFO_URL'),
        'scopes' => env('OIDC_SCOPES', 'openid profile email'),
        'button_label' => env('OIDC_BUTTON_LABEL', 'OIDC'),
    ],
];
