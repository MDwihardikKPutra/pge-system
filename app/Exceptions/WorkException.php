<?php

namespace App\Exceptions;

use Exception;

/**
 * Custom exception for work plan/realization business logic errors
 */
class WorkException extends Exception
{
    public function __construct(string $message = "Work operation error", int $code = 422, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

