<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Annonce;
use App\Mail\ReservationCreated;
use App\Mail\ReservationStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class ReservationController extends Controller
{
    // List reservations for current user (as traveler or host)
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $mesReservations = Reservation::with(['annonce', 'avis'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $reservationsRecues = Reservation::with(['annonce', 'user', 'avis'])
            ->whereHas('annonce', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest()
            ->get();

        $travelerStats = [
            'total' => $mesReservations->count(),
            'pending' => $mesReservations->where('status', 'pending')->count(),
            'upcoming' => $mesReservations
                ->where('status', 'accepted')
                ->filter(fn ($r) => Carbon::parse($r->start_date)->greaterThanOrEqualTo($today))
                ->count(),
            'spent' => $mesReservations->where('status', 'accepted')->sum('total_price'),
        ];

        $hostStats = [
            'received' => $reservationsRecues->count(),
            'pending' => $reservationsRecues->where('status', 'pending')->count(),
            'accepted' => $reservationsRecues->where('status', 'accepted')->count(),
            'revenue' => $reservationsRecues->where('status', 'accepted')->sum('total_price'),
        ];

        return view('reservations.index', compact('mesReservations', 'reservationsRecues', 'travelerStats', 'hostStats'));
    }

    // Store a new reservation with availability check
    public function store(Request $request, $annonceId)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
        ]);

        // Parse dates - handle both d/m/Y (from Flatpickr) and Y-m-d (from tests) formats
        try {
            $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->startOfDay();
        } catch (\Exception $e) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
        }

        try {
            $endDate = Carbon::createFromFormat('d/m/Y', $request->end_date)->startOfDay();
        } catch (\Exception $e) {
            $endDate = Carbon::parse($request->end_date)->startOfDay();
        }

        if ($startDate->isPast()) {
            return back()->withErrors(['start_date' => 'La date d\'arrivée doit être aujourd\'hui ou dans le futur.']);
        }

        if ($endDate <= $startDate) {
            return back()->withErrors(['end_date' => 'La date de départ doit être après la date d\'arrivée.']);
        }

        $annonce = Annonce::findOrFail($annonceId);

        if (Auth::id() === $annonce->user_id) {
            return back()->withErrors(['dates' => 'Vous ne pouvez pas réserver votre propre logement.']);
        }

        $overlap = Reservation::where('annonce_id', $annonce->id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->whereNotIn('status', ['cancelled', 'refused'])
            ->exists();

        if ($overlap) {
            return back()->withErrors(['dates' => 'Ces dates sont déjà réservées pour cette annonce.']);
        }

        $total = Reservation::calculateTotalPrice($startDate, $endDate, $annonce->prix_par_nuit);

        $reservation = Reservation::create([
            'annonce_id'  => $annonce->id,
            'user_id'     => Auth::id(),
            'start_date'  => $startDate->toDateString(),
            'end_date'    => $endDate->toDateString(),
            'total_price' => $total,
            'status'      => 'pending',
        ]);

        // Notify host of new reservation
        Mail::to($annonce->user->email)->send(new ReservationCreated($reservation->load(['annonce', 'user'])));

        return redirect()->route('annonces.show', $annonce)->with('success', 'Réservation demandée avec succès !');
    }

    // Accept a reservation (host only)
    public function accept($id)
    {
        $reservation = Reservation::with(['annonce', 'user'])->findOrFail($id);

        if (Auth::id() !== $reservation->annonce->user_id) {
            return back()->with('error', 'Seul l\'hôte peut accepter la réservation.');
        }

        $reservation->status = 'accepted';
        $reservation->save();

        // Generate and store a receipt snapshot at acceptance time.
        $this->storeReceiptPdf($reservation);

        // Notify traveler
        Mail::to($reservation->user->email)->send(new ReservationStatusChanged($reservation));

        return back()->with('success', 'Réservation acceptée.');
    }

    // Refuse a reservation (host only)
    public function refuse($id)
    {
        $reservation = Reservation::with(['annonce', 'user'])->findOrFail($id);

        if (Auth::id() !== $reservation->annonce->user_id) {
            return back()->with('error', 'Seul l\'hôte peut refuser la réservation.');
        }

        $reservation->status = 'refused';
        $reservation->save();

        // Notify traveler
        Mail::to($reservation->user->email)->send(new ReservationStatusChanged($reservation));

        return back()->with('success', 'Réservation refusée.');
    }

    // Cancel a reservation (traveler only)
    public function cancel($id)
    {
        $reservation = Reservation::with(['annonce', 'user'])->findOrFail($id);

        if (Auth::id() !== $reservation->user_id) {
            return back()->with('error', 'Seul le voyageur peut annuler sa réservation.');
        }

        if (!in_array($reservation->status, ['pending', 'accepted'])) {
            return back()->with('error', 'Cette réservation ne peut pas être annulée.');
        }

        $reservation->status = 'cancelled';
        $reservation->save();

        Mail::to($reservation->annonce->user->email)->send(new ReservationStatusChanged($reservation));

        return back()->with('success', 'Réservation annulée.');
    }

    public function downloadReceipt($id)
    {
        $reservation = Reservation::with(['annonce', 'user'])->findOrFail($id);

        if (Auth::id() !== $reservation->user_id && Auth::id() !== $reservation->annonce->user_id) {
            abort(403, 'Non autorisé');
        }

        if ($reservation->status !== 'accepted') {
            abort(403, 'Le reçu n\'est disponible que pour les réservations confirmées');
        }

        return Pdf::loadView('receipts.reservation', compact('reservation'))->download("receipt-{$reservation->id}.pdf");
    }

    private function storeReceiptPdf(Reservation $reservation): void
    {
        $content = Pdf::loadView('receipts.reservation', compact('reservation'))->output();
        Storage::disk('local')->put("receipts/reservation-{$reservation->id}.pdf", $content);
    }
}