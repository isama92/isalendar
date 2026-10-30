<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class UnexpectedProviderException extends Exception
{
    public function __construct(?int $providerId)
    {
        parent::__construct(sprintf('\'%s\' is not a valid Provider ID.', $providerId));
    }
}
