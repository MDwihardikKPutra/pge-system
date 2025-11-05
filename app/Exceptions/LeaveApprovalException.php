<?php

namespace App\Exceptions;

use Exception;

/**
 * Custom exception for leave approval business logic errors
 */
class LeaveApprovalException extends Exception
{
    public function __construct(string $message = "Leave approval error", int $code = 422, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

