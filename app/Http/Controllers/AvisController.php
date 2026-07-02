<?php

namespace App\Http\Controllers;

use App\Models\Avis;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvisController extends Controller
{
    // Store a new review
    public function store(Request $request, $reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);

        if ($reservation->user_id !== Auth::id() || $reservation->status !== 'accepted') {
            return back()->with('error', 'Vous ne pouvez laisser un avis que pour vos réservations acceptées.');
        }

        if (\Carbon\Carbon::parse($reservation->end_date)->endOfDay()->isFuture()) {
            return back()->with('error', 'Vous pourrez laisser un avis une fois le séjour terminé.');
        }

        if (Avis::where('reservation_id', $reservation->id)->exists()) {
            return back()->with('error', 'Vous avez déjà laissé un avis pour cette réservation.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        Avis::create([
            'reservation_id' => $reservation->id,
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Avis ajouté avec succès.');
    }

    // Delete a review
    public function destroy($id)
    {
        $avis = Avis::findOrFail($id);
        if ($avis->user_id !== Auth::id()) {
            return back()->with('error', 'Vous ne pouvez supprimer que vos propres avis.');
        }
        $avis->delete();
        return back()->with('success', 'Avis supprimé.');
    }
}
