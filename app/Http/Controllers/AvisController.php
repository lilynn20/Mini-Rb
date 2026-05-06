<?php

namespace App\Http\Controllers;

use App\Models\Avis;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvisController extends Controller
{
    public function store(Request $request, $reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);

        if ($reservation->user_id !== Auth::id() || $reservation->status !== 'accepted') {
            return response()->json([
                'message' => 'Vous ne pouvez laisser un avis que pour vos réservations acceptées.',
            ], 403);
        }

        $validated = $request->validate([
            'rating_cleanliness'   => 'required|integer|min:1|max:5',
            'rating_communication' => 'required|integer|min:1|max:5',
            'rating_location'      => 'required|integer|min:1|max:5',
            'rating_value'         => 'required|integer|min:1|max:5',
            'comment'              => 'required|string',
        ]);

        $overall = round((
            $validated['rating_cleanliness'] +
            $validated['rating_communication'] +
            $validated['rating_location'] +
            $validated['rating_value']
        ) / 4);

        Avis::create([
            'reservation_id'       => $reservation->id,
            'user_id'              => Auth::id(),
            'rating'               => $overall,
            'rating_cleanliness'   => $validated['rating_cleanliness'],
            'rating_communication' => $validated['rating_communication'],
            'rating_location'      => $validated['rating_location'],
            'rating_value'         => $validated['rating_value'],
            'comment'              => $validated['comment'],
        ]);

        return response()->json(['message' => 'Avis ajouté avec succès.'], 201);
    }

    public function destroy($id)
    {
        $avis = Avis::findOrFail($id);

        if ($avis->user_id !== Auth::id()) {
            return response()->json(['message' => 'Vous ne pouvez supprimer que vos propres avis.'], 403);
        }

        $avis->delete();

        return response()->json(['message' => 'Avis supprimé.']);
    }
}
