<?php

namespace App\Helpers;

class IconHelper
{
    /**
     * Get icon type for activity based on activity type
     */
    public static function getActivityIconType(string $type): string
    {
        return match($type) {
            'work-plan' => 'clipboard-document',
            'work-realization' => 'check-circle',
            'spd' => 'paper-airplane',
            'purchase' => 'shopping-cart',
            'vendor-payment' => 'credit-card',
            'leave' => 'calendar',
            'project' => 'folder',
            default => 'document'
        };
    }

    /**
     * Get icon type for module
     */
    public static function getModuleIconType(string $moduleKey): string
    {
        return match($moduleKey) {
            'work-plan' => 'clipboard-document',
            'work-realization' => 'check-circle',
            'spd' => 'paper-airplane',
            'purchase' => 'shopping-cart',
            'vendor-payment' => 'credit-card',
            'leave' => 'calendar',
            'leave-approval' => 'document-check',
            'project-management' => 'folder',
            'project-monitoring' => 'chart-bar',
            'payment-approval' => 'document-check',
            'ear' => 'chart-bar',
            'user' => 'user',
            'user-management' => 'user',
            default => 'document'
        };
    }
}
