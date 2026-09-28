<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'status',
    ];

    // ===== Relationships =====

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'category_id');
    }

    // ===== Scopes =====

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    // ===== Helpers =====

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
