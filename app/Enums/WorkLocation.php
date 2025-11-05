<?php

namespace App\Enums;

/**
 * Work Location Enum
 * 
 * Untuk Work Plans - lokasi kerja pengguna
 */
enum WorkLocation: string
{
    case SITE = 'site';
    case OFFICE = 'office';
    case WFH = 'wfh';
    case WFA = 'wfa';
    
    /**
     * Get human-readable label (Indonesian)
     */
    public function label(): string
    {
        return match($this) {
            self::SITE => 'Bekerja di Site/Proyek',
            self::OFFICE => 'Bekerja di Kantor',
            self::WFH => 'Work From Home (WFH)',
            self::WFA => 'Work From Anywhere (WFA)',
        };
    }
    
    /**
     * Get short label
     */
    public function shortLabel(): string
    {
        return match($this) {
            self::SITE => 'Site',
            self::OFFICE => 'Kantor',
            self::WFH => 'WFH',
            self::WFA => 'WFA',
        };
    }
    
    /**
     * Get icon
     */
    public function icon(): string
    {
        return match($this) {
            self::SITE => '🏗️',
            self::OFFICE => '🏢',
            self::WFH => '🏠',
            self::WFA => '🌍',
        };
    }
    
    /**
     * Get badge color
     */
    public function badgeColor(): string
    {
        return match($this) {
            self::SITE => 'bg-blue-100 text-blue-700',
            self::OFFICE => 'bg-purple-100 text-purple-700',
            self::WFH => 'bg-green-100 text-green-700',
            self::WFA => 'bg-orange-100 text-orange-700',
        };
    }
    
    /**
     * Get all values for validation
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}


