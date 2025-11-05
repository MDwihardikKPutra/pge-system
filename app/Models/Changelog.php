<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Changelog extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'release_date',
        'title',
        'changes',
        'category',
        'is_major',
    ];

    protected $casts = [
        'release_date' => 'date',
        'changes' => 'array',
        'is_major' => 'boolean',
    ];

    /**
     * Scope to get major releases only
     */
    public function scopeMajor($query)
    {
        return $query->where('is_major', true);
    }

    /**
     * Scope to get by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to order by release date descending
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('release_date', 'desc')->orderBy('id', 'desc');
    }
}
