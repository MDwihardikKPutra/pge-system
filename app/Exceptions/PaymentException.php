<?php

namespace App\Exceptions;

use Exception;

/**
 * Custom exception for payment operations business logic errors
 */
class PaymentException extends Exception
{
    public function __construct(string $message = "Payment operation error", int $code = 422, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}


