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
        .btn { display: inline-block; background: #f43f5e; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold; }
        .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Mini-Rb</div>
        </div>

        <h2>🏠 Nouvelle demande de réservation</h2>
        <p>Bonjour <strong>{{ $reservation->annonce->user->name }}</strong>,</p>
        <p>Vous avez reçu une nouvelle demande de réservation pour votre annonce :</p>

        <div class="details">
            <p><strong>Annonce :</strong> {{ $reservation->annonce->titre }}</p>
            <p><strong>Voyageur :</strong> {{ $reservation->user->name }}</p>
            <p><strong>Arrivée :</strong> {{ \Carbon\Carbon::parse($reservation->start_date)->format('d/m/Y') }}</p>
            <p><strong>Départ :</strong> {{ \Carbon\Carbon::parse($reservation->end_date)->format('d/m/Y') }}</p>
            <p><strong>Total :</strong> {{ $reservation->total_price }} DH</p>
        </div>

        <p>Connectez-vous pour accepter ou refuser cette réservation.</p>
        <a href="{{ config('app.url') }}/mes-reservations" class="btn">Voir la réservation</a>

        <div class="footer">
            <p>&copy; 2026 Mini-Rb. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>