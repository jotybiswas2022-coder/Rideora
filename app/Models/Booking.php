<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAYMENT_SUBMITTED = 'payment_submitted';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REJECTED = 'rejected';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PAYMENT_SUBMITTED,
        self::STATUS_CONFIRMED,
        self::STATUS_ONGOING,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
        self::STATUS_REJECTED,
    ];

    /**
     * Statuses that hold the vehicle and therefore block overlapping bookings.
     */
    public const BLOCKING_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PAYMENT_SUBMITTED,
        self::STATUS_CONFIRMED,
        self::STATUS_ONGOING,
    ];

    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_REJECTED = 'rejected';
    public const PAYMENT_REFUNDED = 'refunded';

    public const PAYMENT_STATUSES = [
        self::PAYMENT_UNPAID,
        self::PAYMENT_PENDING,
        self::PAYMENT_PAID,
        self::PAYMENT_REJECTED,
        self::PAYMENT_REFUNDED,
    ];

    protected $fillable = [
        'booking_code',
        'user_id',
        'vehicle_id',
        'pickup_location',
        'dropoff_location',
        'pickup_date',
        'pickup_time',
        'return_date',
        'return_time',
        'rental_days',
        'rental_hours',
        'base_amount',
        'security_deposit',
        'discount',
        'total_amount',
        'booking_status',
        'payment_status',
        'customer_note',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'pickup_date' => 'date',
            'return_date' => 'date',
            'base_amount' => 'decimal:2',
            'security_deposit' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'rental_days' => 'integer',
            'rental_hours' => 'integer',
        ];
    }

    // ===== Relationships =====

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest();
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    // ===== Helpers =====

    public function statusLabel(): string
    {
        return match ($this->booking_status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PAYMENT_SUBMITTED => 'Payment Submitted',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_ONGOING => 'Ongoing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_REJECTED => 'Rejected',
            default => ucfirst(str_replace('_', ' ', (string) $this->booking_status)),
        };
    }

    public function statusClass(): string
    {
        return match ($this->booking_status) {
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_PAYMENT_SUBMITTED => 'badge-info',
            self::STATUS_CONFIRMED => 'badge-primary',
            self::STATUS_ONGOING => 'badge-info',
            self::STATUS_COMPLETED => 'badge-success',
            default => 'badge-danger',
        };
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_PAID => 'Paid',
            self::PAYMENT_PENDING => 'Pending Verification',
            self::PAYMENT_REJECTED => 'Rejected',
            self::PAYMENT_REFUNDED => 'Refunded',
            default => 'Unpaid',
        };
    }

    public function paymentStatusClass(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_PAID => 'badge-success',
            self::PAYMENT_PENDING => 'badge-warning',
            self::PAYMENT_REJECTED, self::PAYMENT_REFUNDED => 'badge-danger',
            default => 'badge-muted',
        };
    }

    /**
     * Customers may cancel while the rental has not started yet.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->booking_status, [
            self::STATUS_PENDING,
            self::STATUS_PAYMENT_SUBMITTED,
            self::STATUS_CONFIRMED,
        ], true);
    }

    public function canBePaid(): bool
    {
        return in_array($this->booking_status, [self::STATUS_PENDING, self::STATUS_REJECTED], true)
            && $this->payment_status !== self::PAYMENT_PAID;
    }

    public function canBeReviewed(): bool
    {
        return $this->booking_status === self::STATUS_COMPLETED && ! $this->review()->exists();
    }

    public function durationLabel(): string
    {
        $days = $this->rental_days;
        $hours = $this->rental_hours;

        if ($days > 0 && $hours > 0) {
            return $days.' '.($days === 1 ? 'day' : 'days').' '.$hours.' '.($hours === 1 ? 'hour' : 'hours');
        }

        if ($days > 0) {
            return $days.' '.($days === 1 ? 'day' : 'days');
        }

        return max($hours, 1).' '.($hours === 1 ? 'hour' : 'hours');
    }

    public function isOverdue(): bool
    {
        return $this->booking_status === self::STATUS_ONGOING && $this->return_date->isPast();
    }
}
