<?php

namespace App\Actions;

class GetThrottleLimitKey
{
    /**
     * @param  array<int, int|string>  $keyComponents
     */
    public function execute(string $limiterName, array $keyComponents = []): string
    {
        $keyString = collect($keyComponents)->map(fn (): string => '%s')->join(':');

        $key = sprintf($keyString, ...$keyComponents);

        return md5(sprintf('%s%s', $limiterName, $key));
    }
}
