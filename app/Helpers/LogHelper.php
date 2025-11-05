<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class LogHelper
{
    /**
     * Log error with consistent format and context
     *
     * @param string $message
     * @param \Exception $exception
     * @param array $additionalContext
     * @return void
     */
    public static function logError(string $message, \Exception $exception, array $additionalContext = []): void
    {
        $context = array_merge([
            'user_id' => auth()->id(),
            'exception_message' => $exception->getMessage(),
            'exception_trace' => $exception->getTraceAsString(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ], $additionalContext);

        Log::error($message, $context);
    }

    /**
     * Log error for controller operations
     *
     * @param string $operation (e.g., 'creating', 'updating', 'deleting')
     * @param string $modelName (e.g., 'Purchase', 'WorkPlan')
     * @param \Exception $exception
     * @param int|null $modelId
     * @param array|null $requestData
     * @return void
     */
    public static function logControllerError(
        string $operation,
        string $modelName,
        \Exception $exception,
        ?int $modelId = null,
        ?array $requestData = null
    ): void {
        $message = "Error {$operation} {$modelName}";
        
        $context = [
            'model' => $modelName,
            'operation' => $operation,
        ];

        if ($modelId) {
            $context['model_id'] = $modelId;
        }

        if ($requestData) {
            // Exclude sensitive data and files
            $context['request_data'] = array_filter($requestData, function($key) {
                return !in_array($key, ['_token', 'password', 'password_confirmation', 'documents', 'attachment', 'output_files']);
            }, ARRAY_FILTER_USE_KEY);
        }

        self::logError($message, $exception, $context);
    }
}

