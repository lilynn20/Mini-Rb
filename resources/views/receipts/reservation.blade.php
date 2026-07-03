<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #f43f5e; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { color: #f43f5e; font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .section { margin-bottom: 25px; }
        .section-title { font-weight: bold; font-size: 13px; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 10px; }
        .row { display: flex; justify-content: space-between; padding: 6px 0; }
        .label { font-weight: bold; }
        .price-section { background: #f9f9f9; padding: 20px; border-radius: 5px; }
        .total-row { font-weight: bold; font-size: 15px; border-top: 1px solid #ddd; padding-top: 10px; }
        .footer { text-align: center; color: #999; font-size: 10px; margin-top: 40px; border-top: 1px solid #ddd; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Mini-Rb</div>
        <div style="color: #666;">Reçu de Réservation</div>
        <div style="color: #666; font-size: 12px;">Ref: #{{ $reservation->id }}</div>
    </div>

    <div class="section">
        <div class="section-title">Propriété</div>
        <div class="row">
            <span class="label">Titre:</span>
            <span>{{ $reservation->annonce->titre }}</span>
        </div>
        <div class="row">
            <span class="label">Adresse:</span>
            <span>{{ $reservation->annonce->adresse }}, {{ $reservation->annonce->ville }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Détails de Réservation</div>
        <div class="row">
            <span class="label">Voyageur:</span>
            <span>{{ $reservation->user->name }} ({{ $reservation->user->email }})</span>
        </div>
        <div class="row">
            <span class="label">Arrivée:</span>
            <span>{{ \Carbon\Carbon::parse($reservation->start_date)->format('d/m/Y') }}</span>
        </div>
        <div class="row">
            <span class="label">Départ:</span>
            <span>{{ \Carbon\Carbon::parse($reservation->end_date)->format('d/m/Y') }}</span>
        </div>
        <div class="row">
            <span class="label">Nuits:</span>
            <span>{{ \Carbon\Carbon::parse($reservation->start_date)->diffInDays(\Carbon\Carbon::parse($reservation->end_date)) }}</span>
        </div>
    </div>

    <div class="price-section">
        <div class="row">
            <span class="label">Prix par nuit:</span>
            <span>{{ $reservation->annonce->prix_par_nuit }} DH</span>
        </div>
        <div class="row" style="margin-top: 10px; margin-bottom: 10px;">
            <span class="label">Total:</span>
            <span>{{ $reservation->total_price }} DH</span>
        </div>
        <div class="row total-row">
            <span>À payer:</span>
            <span>{{ $reservation->total_price }} DH</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Statut</div>
        <div class="row">
            <span class="label">Statut de Réservation:</span>
            <span>{{ ucfirst($reservation->status) }}</span>
        </div>
        <div class="row">
            <span class="label">Date de Réservation:</span>
            <span>{{ $reservation->created_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    <div class="footer">
        <p>Merci d'avoir choisi Mini-Rb!</p>
        <p>&copy; 2026 Mini-Rb. Tous droits réservés.</p>
        <p>Généré le {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
