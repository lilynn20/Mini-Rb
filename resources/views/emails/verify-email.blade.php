<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; padding: 40px; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { color: #f43f5e; font-size: 28px; font-weight: bold; }
        .btn { display: inline-block; background: #f43f5e; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold; }
        .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Mini-Rb</div>
        </div>

        <h2>✉️ Vérifiez votre adresse email</h2>
        <p>Bonjour <strong>{{ $userName }}</strong>,</p>
        <p>Merci de vous être inscrit sur Mini-Rb. Cliquez sur le bouton ci-dessous pour vérifier votre adresse email et activer votre compte.</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $verificationUrl }}" class="btn">Vérifier mon email</a>
        </div>

        <p style="color: #999; font-size: 13px;">Ce lien expire dans 60 minutes. Si vous n'avez pas créé de compte, ignorez cet email.</p>

        <div class="footer">
            <p>&copy; 2026 Mini-Rb. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>