<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Annonce;
use App\Mail\ReservationCreated;
use App\Mail\ReservationStatusChanged;
use App\Http\Requests\StoreReservationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ReservationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $mesReservations = Reservation::with(['annonce.images', 'avis'])
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(fn ($r) => $this->formatReservation($r, false));

        $reservationsRecues = Reservation::with(['annonce.images', 'user', 'avis'])
            ->whereHas('annonce', fn ($q) => $q->where('user_id', $user->id))
            ->latest()
            ->get()
            ->map(fn ($r) => $this->formatReservation($r, true));

        return response()->json([
            'mes_reservations'     => $mesReservations,
            'reservations_recues'  => $reservationsRecues,
        ]);
    }

    public function store(StoreReservationRequest $request, $annonceId)
    {
        $annonce = Annonce::findOrFail($annonceId);

        if (Auth::id() === $annonce->user_id) {
            return response()->json([
                'errors' => ['dates' => ['Vous ne pouvez pas réserver votre propre logement.']],
            ], 422);
        }

        $maxCapacity = $annonce->nombre_de_chambres * 2;
        if ($request->nb_voyageurs > $maxCapacity) {
            return response()->json([
                'errors' => ['nb_voyageurs' => [
                    "Ce logement accepte au maximum {$maxCapacity} voyageurs (" . $annonce->nombre_de_chambres . ' chambre(s)).',
                ]],
            ], 422);
        }

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
            return response()->json([
                'errors' => ['dates' => ['Ces dates sont déjà réservées pour cette annonce.']],
            ], 422);
        }

        $total = Reservation::calculateTotalPrice($request->start_date, $request->end_date, $annonce->prix_par_nuit);

        $reservation = Reservation::create([
            'annonce_id'   => $annonce->id,
            'user_id'      => Auth::id(),
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date,
            'nb_voyageurs' => $request->nb_voyageurs,
            'total_price'  => $total,
            'status'       => 'pending',
        ]);

        Mail::to($annonce->user->email)->send(new ReservationCreated($reservation->load(['annonce', 'user'])));

        return response()->json(['message' => 'Réservation demandée avec succès !'], 201);
    }

    public function accept($id)
    {
        $reservation = Reservation::with(['annonce', 'user'])->findOrFail($id);

        if (Auth::id() !== $reservation->annonce->user_id) {
            return response()->json(['message' => 'Seul l\'hôte peut accepter la réservation.'], 403);
        }

        $reservation->status = 'accepted';
        $reservation->save();

        Mail::to($reservation->user->email)->send(new ReservationStatusChanged($reservation));

        return response()->json(['message' => 'Réservation acceptée.']);
    }

    public function refuse($id)
    {
        $reservation = Reservation::with(['annonce', 'user'])->findOrFail($id);

        if (Auth::id() !== $reservation->annonce->user_id) {
            return response()->json(['message' => 'Seul l\'hôte peut refuser la réservation.'], 403);
        }

        $reservation->status = 'refused';
        $reservation->save();

        Mail::to($reservation->user->email)->send(new ReservationStatusChanged($reservation));

        return response()->json(['message' => 'Réservation refusée.']);
    }

    public function cancel($id)
    {
        $reservation = Reservation::with(['annonce', 'user'])->findOrFail($id);

        if (Auth::id() !== $reservation->user_id) {
            return response()->json(['message' => 'Seul le voyageur peut annuler sa réservation.'], 403);
        }

        if (!in_array($reservation->status, ['pending', 'accepted'])) {
            return response()->json(['message' => 'Cette réservation ne peut pas être annulée.'], 422);
        }

        $reservation->status = 'cancelled';
        $reservation->save();

        Mail::to($reservation->annonce->user->email)->send(new ReservationStatusChanged($reservation));

        return response()->json(['message' => 'Réservation annulée.']);
    }

    private function formatReservation(Reservation $r, bool $includeTraveler): array
    {
        $userId = Auth::id();
        $hasMyReview = $r->avis->where('user_id', $userId)->isNotEmpty();
        $firstImage = $r->annonce->images->first();

        $data = [
            'id'           => $r->id,
            'start_date'   => $r->start_date,
            'end_date'     => $r->end_date,
            'nb_voyageurs' => $r->nb_voyageurs,
            'total_price'  => $r->total_price,
            'status'       => $r->status,
            'has_my_review' => $hasMyReview,
            'annonce'     => [
                'id'        => $r->annonce->id,
                'titre'     => $r->annonce->titre,
                'ville'     => $r->annonce->ville,
                'image_url' => $firstImage ? Storage::disk('s3')->url($firstImage->path) : null,
            ],
        ];

        if ($includeTraveler) {
            $data['traveler_name'] = $r->user->name;
        }

        return $data;
    }
}
