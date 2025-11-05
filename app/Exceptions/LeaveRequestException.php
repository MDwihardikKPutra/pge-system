<?php

namespace App\Exceptions;

use Exception;

/**
 * Custom exception for leave request business logic errors
 */
class LeaveRequestException extends Exception
{
    public function __construct(string $message = "Leave request error", int $code = 422, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

