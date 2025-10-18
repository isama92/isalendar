<?php

use App\Actions\GetThrottleLimitKey;

uses()->group('arch', 'architecture');

arch()->preset()->php();

arch()->preset()->security()
    ->ignoring([
        'assert',
        'unserialize',
        'parse_str',
        GetThrottleLimitKey::class,
    ]);
