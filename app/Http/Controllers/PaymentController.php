<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function checkout(Request $request, $id)
    {
        $reservation = Reservation::with('annonce')->findOrFail($id);

        if ($reservation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        if ($reservation->status !== 'accepted') {
            return response()->json(['message' => 'La réservation doit être acceptée par l\'hôte avant le paiement.'], 422);
        }

        if ($reservation->paid_at) {
            return response()->json(['message' => 'Cette réservation est déjà payée.'], 422);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = StripeSession::create([
            'mode'                 => 'payment',
            'payment_method_types' => ['card'],
            'line_items'           => [[
                'price_data' => [
                    'currency'     => 'mad',
                    'product_data' => [
                        'name'        => 'Réservation: ' . $reservation->annonce->titre,
                        'description' => 'Du ' . $reservation->start_date . ' au ' . $reservation->end_date
                            . ' (' . $reservation->nb_voyageurs . ' voyageur'
                            . ($reservation->nb_voyageurs > 1 ? 's' : '') . ')',
                    ],
                    'unit_amount' => (int) round($reservation->total_price * 100),
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'reservation_id' => $reservation->id,
            ],
            'success_url' => url('/mes-reservations?paid=1&reservation_id=' . $reservation->id . '&session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url'  => url('/mes-reservations?cancelled=1'),
        ]);

        $reservation->stripe_session_id = $session->id;
        $reservation->save();

        return response()->json([
            'checkout_url' => $session->url,
        ]);
    }

    public function paymentSuccess(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        if ($reservation->paid_at) {
            return response()->json(['paid' => true, 'paid_at' => $reservation->paid_at]);
        }

        $sessionId = $request->query('session_id');
        if (!$sessionId || $sessionId !== $reservation->stripe_session_id) {
            return response()->json(['message' => 'Session invalide.'], 422);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = StripeSession::retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            return response()->json([
                'paid'    => false,
                'message' => 'Le paiement n\'est pas confirmé (statut: ' . $session->payment_status . ').',
            ], 422);
        }

        $reservation->paid_at = now();
        $reservation->save();

        return response()->json([
            'paid'    => true,
            'message' => 'Paiement confirmé !',
            'paid_at' => $reservation->paid_at,
        ]);
    }
}
