<?php

namespace App\Constants;

class ProjectAccessType
{
    const PM = 'pm';
    const FINANCE = 'finance';
    const FULL = 'full';

    /**
     * Get all access types
     */
    public static function all(): array
    {
        return [
            self::PM,
            self::FINANCE,
            self::FULL,
        ];
    }

    /**
     * Get access type labels
     */
    public static function labels(): array
    {
        return [
            self::PM => 'Project Manager (Work Plans & Realizations)',
            self::FINANCE => 'Finance (Payments Only)',
            self::FULL => 'Full Access (All)',
        ];
    }

    /**
     * Get label for specific access type
     */
    public static function label(string $accessType): ?string
    {
        return self::labels()[$accessType] ?? null;
    }

    /**
     * Check if access type is valid
     */
    public static function isValid(string $accessType): bool
    {
        return in_array($accessType, self::all());
    }

    /**
     * Get access types that can access work
     */
    public static function workAccessTypes(): array
    {
        return [self::PM, self::FULL];
    }

    /**
     * Get access types that can access payments
     */
    public static function paymentAccessTypes(): array
    {
        return [self::FINANCE, self::FULL];
    }

    /**
     * Check if access type can access work
     */
    public static function canAccessWork(string $accessType): bool
    {
        return in_array($accessType, self::workAccessTypes());
    }

    /**
     * Check if access type can access payments
     */
    public static function canAccessPayments(string $accessType): bool
    {
        return in_array($accessType, self::paymentAccessTypes());
    }
}

