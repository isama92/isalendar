<?php

namespace App\Csp;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policy;
use Spatie\Csp\Preset;

class Horizon implements Preset
{
    public function configure(Policy $policy): void
    {
        // Disable CSP entirely for Horizon routes to avoid maintenance issues
        // when Horizon updates change inline script/style hashes
        if (str(request()->path())->startsWith('horizon')) {
            $policy->add(Directive::DEFAULT, [Keyword::UNSAFE_INLINE, Keyword::UNSAFE_EVAL, '*']);
        }
    }
}
