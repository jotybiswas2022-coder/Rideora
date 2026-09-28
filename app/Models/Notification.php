<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    public const TYPE_GENERAL = 'general';
    public const TYPE_BOOKING = 'booking';
    public const TYPE_PAYMENT = 'payment';
    public const TYPE_REVIEW = 'review';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'link',
        'is_read',
    ];

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function icon(): string
    {
        return match ($this->type) {
            self::TYPE_PAYMENT => 'cash-stack',
            self::TYPE_BOOKING => 'car-front',
            self::TYPE_REVIEW => 'star-fill',
            default => 'bell',
        };
    }

    /**
     * Create a notification for a user.
     */
    public static function notify(int $userId, string $title, string $message, string $type = self::TYPE_GENERAL, ?string $link = null): self
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'is_read' => false,
        ]);
    }
}
