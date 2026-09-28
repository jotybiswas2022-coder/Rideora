<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BookingService
{
    /**
     * Multi-day discount tiers: minimum days => percentage off the base amount.
     *
     * @var array<int, float>
     */
    public const DISCOUNT_TIERS = [
        30 => 15.0,
        14 => 10.0,
        7 => 5.0,
    ];

    /**
     * Build a full price quote on the server. Never trust submitted prices.
     *
     * @return array<string, mixed>
     */
    public function quote(Vehicle $vehicle, string $pickupDate, string $pickupTime, string $returnDate, string $returnTime): array
    {
        $pickup = Carbon::parse($pickupDate.' '.($pickupTime ?: '00:00'));
        $return = Carbon::parse($returnDate.' '.($returnTime ?: '00:00'));

        if ($return->lessThanOrEqualTo($pickup)) {
            throw new InvalidArgumentException('The return date and time must be after the pickup date and time.');
        }

        $fullDays = (int) $pickup->copy()->startOfDay()->diffInDays($return->copy()->startOfDay());

        if ($fullDays >= 1) {
            $days = $fullDays;
            $hours = 0;
            $base = $days * (float) $vehicle->price_per_day;

            // Leftover hours on the final day are charged hourly when they exist.
            $leftover = (int) $return->diffInHours($pickup);
            $leftover -= $days * 24;
            if ($leftover > 0) {
                $hours = $leftover;
                $base += $leftover * (float) $vehicle->price_per_hour;
            }
        } else {
            $days = 0;
            $hours = max(1, (int) $return->diffInHours($pickup));
            $base = $hours * (float) $vehicle->price_per_hour;
        }

        $deposit = (float) $vehicle->security_deposit;
        $discountPercent = $this->discountPercentForDays($days);
        $discount = round($base * ($discountPercent / 100), 2);
        $total = round($base - $discount + $deposit, 2);

        return [
            'pickup_at' => $pickup,
            'return_at' => $return,
            'rental_days' => $days,
            'rental_hours' => $hours,
            'base_amount' => round($base, 2),
            'security_deposit' => round($deposit, 2),
            'discount' => $discount,
            'discount_percent' => $discountPercent,
            'total_amount' => $total,
        ];
    }

    public function discountPercentForDays(int $days): float
    {
        foreach (self::DISCOUNT_TIERS as $minimumDays => $percent) {
            if ($days >= $minimumDays) {
                return $percent;
            }
        }

        return 0.0;
    }

    /**
     * Is the vehicle free for the requested window, ignoring cancelled/rejected bookings?
     */
    public function isAvailable(Vehicle $vehicle, string $pickupDate, string $returnDate, ?int $ignoreBookingId = null): bool
    {
        if ($vehicle->status === Vehicle::STATUS_INACTIVE || $vehicle->status === Vehicle::STATUS_MAINTENANCE) {
            return false;
        }

        return $vehicle->isFreeBetween(
            Carbon::parse($pickupDate)->toDateString(),
            Carbon::parse($returnDate)->toDateString(),
            $ignoreBookingId
        );
    }

    /**
     * Create a booking with a unique code, computed by the server.
     *
     * @param  array<string, mixed>  $data
     */
    public function createBooking(Vehicle $vehicle, int $userId, array $data): Booking
    {
        $quote = $this->quote(
            $vehicle,
            $data['pickup_date'],
            $data['pickup_time'] ?? '00:00',
            $data['return_date'],
            $data['return_time'] ?? '00:00'
        );

        return DB::transaction(function () use ($vehicle, $userId, $data, $quote) {
            $booking = Booking::create([
                'booking_code' => $this->generateBookingCode(),
                'user_id' => $userId,
                'vehicle_id' => $vehicle->id,
                'pickup_location' => $data['pickup_location'],
                'dropoff_location' => $data['dropoff_location'] ?? $data['pickup_location'],
                'pickup_date' => $quote['pickup_at']->toDateString(),
                'pickup_time' => $quote['pickup_at']->format('H:i:s'),
                'return_date' => $quote['return_at']->toDateString(),
                'return_time' => $quote['return_at']->format('H:i:s'),
                'rental_days' => $quote['rental_days'],
                'rental_hours' => $quote['rental_hours'],
                'base_amount' => $quote['base_amount'],
                'security_deposit' => $quote['security_deposit'],
                'discount' => $quote['discount'],
                'total_amount' => $quote['total_amount'],
                'booking_status' => Booking::STATUS_PENDING,
                'payment_status' => Booking::PAYMENT_UNPAID,
                'customer_note' => $data['customer_note'] ?? null,
            ]);

            return $booking;
        });
    }

    /**
     * Sequential, human friendly booking code, e.g. VR-20260928-0001
     */
    public function generateBookingCode(): string
    {
        $date = Carbon::now();
        $prefix = 'VR-'.$date->format('Ymd').'-';

        $lastCode = Booking::query()
            ->where('booking_code', 'like', $prefix.'%')
            ->orderByDesc('booking_code')
            ->value('booking_code');

        $sequence = $lastCode ? ((int) substr($lastCode, -4)) + 1 : 1;
        $code = $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

        // Guard against collisions if a code already exists.
        while (Booking::where('booking_code', $code)->exists()) {
            $sequence++;
            $code = $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
        }

        return $code;
    }
}
