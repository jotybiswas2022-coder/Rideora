<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicle extends Model
{
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_BOOKED = 'booked';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_INACTIVE = 'inactive';

    public const STATUSES = [
        self::STATUS_AVAILABLE,
        self::STATUS_BOOKED,
        self::STATUS_MAINTENANCE,
        self::STATUS_INACTIVE,
    ];

    public const FUEL_TYPES = ['Petrol', 'Diesel', 'Octane', 'CNG', 'Hybrid', 'Electric'];
    public const TRANSMISSIONS = ['Manual', 'Automatic', 'CVT', 'AMT'];
    public const VEHICLE_TYPES = ['Car', 'SUV', 'Hatchback', 'Microbus', 'Bike', 'Pickup', 'Luxury', 'Other'];

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'brand',
        'model',
        'registration_number',
        'vehicle_type',
        'fuel_type',
        'transmission',
        'seats',
        'price_per_hour',
        'price_per_day',
        'security_deposit',
        'location',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price_per_hour' => 'decimal:2',
            'price_per_day' => 'decimal:2',
            'security_deposit' => 'decimal:2',
            'seats' => 'integer',
        ];
    }

    // ===== Relationships =====

    public function category(): BelongsTo
    {
        return $this->belongsTo(VehicleCategory::class, 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class)->orderByDesc('is_primary')->orderBy('id');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(VehicleImage::class)->where('is_primary', true);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('status', 'approved')->latest();
    }

    // ===== Scopes =====

    public function scopeListable(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_AVAILABLE, self::STATUS_BOOKED]);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    // ===== Helpers =====

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    /**
     * Human label for the vehicle status.
     */
    public function statusLabel(): string
    {
        return ucfirst($this->status);
    }

    public function statusClass(): string
    {
        return match ($this->status) {
            self::STATUS_AVAILABLE => 'badge-success',
            self::STATUS_BOOKED => 'badge-warning',
            self::STATUS_MAINTENANCE => 'badge-danger',
            default => 'badge-muted',
        };
    }

    public function imageUrl(): string
    {
        $image = $this->relationLoaded('images')
            ? ($this->images->firstWhere('is_primary', true) ?? $this->images->first())
            : ($this->primaryImage ?? $this->images()->first());

        return $image ? $image->url() : self::placeholderImage();
    }

    public static function placeholderImage(): string
    {
        return 'data:image/svg+xml;utf8,'.rawurlencode(
            '<svg xmlns="http://www.w3.org/2000/svg" width="640" height="420">'
            .'<rect width="100%" height="100%" fill="#eef2f7"/>'
            .'<text x="50%" y="50%" fill="#94a3b8" font-family="sans-serif" font-size="26" '
            .'text-anchor="middle" dominant-baseline="middle">No image</text></svg>'
        );
    }

    public function averageRating(): float
    {
        $average = $this->reviews()->where('status', 'approved')->avg('rating');

        return $average ? round((float) $average, 1) : 0.0;
    }

    public function reviewsCount(): int
    {
        return $this->reviews()->where('status', 'approved')->count();
    }

    /**
     * Check whether the vehicle is free for the given date window.
     * Overlapping pending/payment_submitted/confirmed/ongoing bookings block a new booking.
     */
    public function isFreeBetween(string $pickupDate, string $returnDate, ?int $ignoreBookingId = null): bool
    {
        return ! $this->bookings()
            ->whereIn('booking_status', Booking::BLOCKING_STATUSES)
            ->when($ignoreBookingId, fn ($query) => $query->whereKeyNot($ignoreBookingId))
            ->where('pickup_date', '<', $returnDate)
            ->where('return_date', '>', $pickupDate)
            ->exists();
    }

    /**
     * Dates already blocked by active bookings (used to build the availability UI).
     *
     * @return array<int, array<string, string>>
     */
    public function blockedRanges(): array
    {
        return $this->bookings()
            ->whereIn('booking_status', Booking::BLOCKING_STATUSES)
            ->orderBy('pickup_date')
            ->get(['pickup_date', 'return_date'])
            ->map(fn ($booking) => [
                'from' => $booking->pickup_date->toDateString(),
                'to' => $booking->return_date->toDateString(),
            ])
            ->all();
    }
}
