<?php

namespace Tests\Helpers;

use Illuminate\Contracts\Cache\Lock as LockContract;

class ImmediateLock implements LockContract
{
    public function get($callback = null)
    {
        return $callback ? $callback() : true;
    }

    public function block($seconds, $callback = null)
    {
        if ($callback) {
            return $callback();
        }

        // When no callback is provided, block() should return whether the lock was acquired.
        return true;
    }

    public function release()
    {
        return true;
    }

    public function owner()
    {
        return null;
    }

    public function forceRelease()
    {
        return true;
    }
}
