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
        'total_price',
        'status',
    ];

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public static function calculateTotalPrice($startDate, $endDate, $pricePerNight)
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        $nights = $start->diffInDays($end);
        return $nights * $pricePerNight;
    }
}