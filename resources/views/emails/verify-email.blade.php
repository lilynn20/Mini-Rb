<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification email - Mini-Rb</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, sans-serif;">
    <div style="max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
        <div style="font-size: 24px; font-weight: 700; color: #f43f5e; margin-bottom: 20px;">Mini-Rb</div>

        <h2 style="margin: 0 0 12px; font-size: 24px; color: #111827;">Bonjour {{ $userName }},</h2>
        <p style="margin: 0 0 16px; color: #4b5563; line-height: 1.6;">
            Merci pour votre inscription sur Mini-Rb. Pour activer votre compte, veuillez vérifier votre adresse email en cliquant sur le bouton ci-dessous.
        </p>

        <p style="margin: 0 0 20px;">
            <a href="{{ $verificationUrl }}" style="display: inline-block; background-color: #f43f5e; color: #ffffff; text-decoration: none; padding: 12px 20px; border-radius: 999px; font-weight: 600;">
                Vérifier mon email
            </a>
        </p>

        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px; line-height: 1.6;">
            Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :
        </p>
        <p style="margin: 0; color: #6b7280; font-size: 14px; word-break: break-all;">
            {{ $verificationUrl }}
        </p>
    </div>
</body>
</html>