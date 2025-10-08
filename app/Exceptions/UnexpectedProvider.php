<?php

namespace App\Exceptions;

use Exception;

class UnexpectedProvider extends Exception
{
    public function __construct(?int $providerId)
    {
        parent::__construct(sprintf('\'%s\' is not a valid Provider ID.', $providerId));
    }
}
