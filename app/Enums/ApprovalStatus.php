<?php

namespace App\Enums;

/**
 * Approval Status Enum
 * 
 * Centralized status management untuk semua approval flows
 */
enum ApprovalStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    
    /**
     * Get human-readable label (Indonesian)
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Persetujuan',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
        };
    }
    
    /**
     * Get badge color class for UI
     */
    public function badgeColor(): string
    {
        return match($this) {
            self::PENDING => 'bg-yellow-100 text-yellow-700',
            self::APPROVED => 'bg-green-100 text-green-700',
            self::REJECTED => 'bg-red-100 text-red-700',
        };
    }
    
    /**
     * Get icon for UI
     */
    public function icon(): string
    {
        return match($this) {
            self::PENDING => '⏳',
            self::APPROVED => '✓',
            self::REJECTED => '✗',
        };
    }
    
    /**
     * Check if status is pending
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }
    
    /**
     * Check if status is approved
     */
    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }
    
    /**
     * Check if status is rejected
     */
    public function isRejected(): bool
    {
        return $this === self::REJECTED;
    }
    
    /**
     * Get all status values (for validation rules)
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}


