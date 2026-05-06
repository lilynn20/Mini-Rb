<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'annonce_id',
        'user_id',
        'start_date',
        'end_date',
        'nb_voyageurs',
        'total_price',
        'status',
        'paid_at',
        'stripe_session_id',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function avis()
    {
        return $this->hasMany(\App\Models\Avis::class);
    }

    public static function getBlockedDates($annonceId)
    {
        $blocked = [];
        $reservations = self::where('annonce_id', $annonceId)
            ->whereIn('status', ['pending', 'accepted'])
            ->get(['start_date', 'end_date']);
        foreach ($reservations as $res) {
            $start = \Carbon\Carbon::parse($res->start_date);
            $end = \Carbon\Carbon::parse($res->end_date);
            for ($date = $start->copy(); $date < $end; $date->addDay()) {
                $blocked[] = $date->toDateString();
            }
        }
        return $blocked;
    }

    const CLEANING_FEE = 200;
    const SERVICE_FEE_RATE = 0.10;

    public static function calculateTotalPrice($startDate, $endDate, $pricePerNight)
    {
        $breakdown = self::priceBreakdown($startDate, $endDate, $pricePerNight);
        return $breakdown['total'];
    }

    public static function priceBreakdown($startDate, $endDate, $pricePerNight): array
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        $nights = $start->diffInDays($end);
        $subtotal = $nights * $pricePerNight;
        $cleaning = self::CLEANING_FEE;
        $service = round($subtotal * self::SERVICE_FEE_RATE, 2);

        return [
            'nights'        => $nights,
            'price_per_night' => (float) $pricePerNight,
            'subtotal'      => round($subtotal, 2),
            'cleaning_fee'  => $cleaning,
            'service_fee'   => $service,
            'total'         => round($subtotal + $cleaning + $service, 2),
        ];
    }
}