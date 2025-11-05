<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company',
        'email',
        'phone',
        'bank_name',
        'account_number',
        'account_holder',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function vendorPayments()
    {
        return $this->hasMany(VendorPayment::class);
    }

    /**
     * Scope untuk mengambil vendor yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk mengambil vendor yang diurutkan berdasarkan nama
     */
    public function scopeOrderedByName($query)
    {
        return $query->orderBy('name');
    }
}
