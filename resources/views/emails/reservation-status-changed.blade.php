<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; padding: 40px; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { color: #f43f5e; font-size: 28px; font-weight: bold; }
        .details { background: #f9f9f9; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .status-accepted { color: #16a34a; font-weight: bold; font-size: 18px; }
        .status-refused { color: #dc2626; font-weight: bold; font-size: 18px; }
        .status-cancelled { color: #6b7280; font-weight: bold; font-size: 18px; }
        .btn { display: inline-block; background: #f43f5e; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold; }
        .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Mini-Rb</div>
        </div>

        @php
            $statusText = match($reservation->status) {
                'accepted'  => '✅ Votre réservation a été acceptée !',
                'refused'   => '❌ Votre réservation a été refusée.',
                'cancelled' => '🚫 Votre réservation a été annulée.',
                default     => 'Votre réservation a été mise à jour.',
            };
            $statusClass = 'status-' . $reservation->status;
        @endphp

        <h2>Mise à jour de votre réservation</h2>
        <p>Bonjour <strong>{{ $reservation->user->name }}</strong>,</p>
        <p class="{{ $statusClass }}">{{ $statusText }}</p>

        <div class="details">
            <p><strong>Annonce :</strong> {{ $reservation->annonce->titre }}</p>
            <p><strong>Ville :</strong> {{ $reservation->annonce->ville }}</p>
            <p><strong>Arrivée :</strong> {{ \Carbon\Carbon::parse($reservation->start_date)->format('d/m/Y') }}</p>
            <p><strong>Départ :</strong> {{ \Carbon\Carbon::parse($reservation->end_date)->format('d/m/Y') }}</p>
            <p><strong>Total :</strong> {{ $reservation->total_price }} DH</p>
        </div>

        <a href="{{ config('app.url') }}/mes-reservations" class="btn">Voir mes réservations</a>

        <div class="footer">
            <p>&copy; 2026 Mini-Rb. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>