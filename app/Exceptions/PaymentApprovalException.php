<?php

namespace App\Exceptions;

use Exception;

/**
 * Custom exception for payment approval business logic errors
 */
class PaymentApprovalException extends Exception
{
    public function __construct(string $message = "Payment approval error", int $code = 422, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}


