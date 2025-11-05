<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Module extends Model
{
    protected $fillable = [
        'key',
        'label',
        'icon',
        'description',
        'routes',
        'actions',
        'assignable_to_user',
        'admin_only',
        'is_default',
        'is_active',
        'sort_order',
        'category',
    ];

    protected $casts = [
        'routes' => 'array',
        'actions' => 'array',
        'assignable_to_user' => 'boolean',
        'admin_only' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Users yang memiliki modul ini
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Scope untuk modul yang bisa di-assign ke user
     */
    public function scopeAssignableToUser($query)
    {
        return $query->where('assignable_to_user', true)->where('is_active', true);
    }

    /**
     * Scope untuk modul admin-only
     */
    public function scopeAdminOnly($query)
    {
        return $query->where('admin_only', true)->where('is_active', true);
    }

    /**
     * Scope untuk modul default
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true)->where('is_active', true);
    }

    /**
     * Scope untuk modul aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

