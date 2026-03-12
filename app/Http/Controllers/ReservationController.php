<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Annonce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    // Store a new reservation with availability check
    public function store(Request $request, $annonceId)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        $annonce = Annonce::findOrFail($annonceId);

        // Check for overlapping reservations
        $overlap = Reservation::where('annonce_id', $annonce->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                      });
            })
            ->whereNotIn('status', ['cancelled', 'refused'])
            ->exists();

        if ($overlap) {
            return back()->withErrors(['dates' => 'Ces dates sont déjà réservées pour cette annonce.']);
        }

        // Calculate total price
        $days = (new \DateTime($request->start_date))->diff(new \DateTime($request->end_date))->days;
        $total = $days * $annonce->prix_par_nuit;

        Reservation::create([
            'annonce_id' => $annonce->id,
            'user_id' => Auth::id(),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_price' => $total,
            'status' => 'pending',
        ]);

        return redirect()->route('annonces.show', $annonce)->with('success', 'Réservation demandée !');
    }
}
