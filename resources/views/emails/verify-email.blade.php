<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification email - Mini-Rb</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; padding: 40px 20px; margin: 0;">
    <table role="presentation" style="max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <tr>
            <td style="padding: 40px 40px 32px; text-align: center;">
                <div style="font-size: 28px; font-weight: bold; color: #f43f5e; letter-spacing: -0.5px;">Mini-Rb</div>
            </td>
        </tr>
        <tr>
            <td style="padding: 0 40px 32px;">
                <h2 style="margin: 0 0 16px; font-size: 22px; color: #111827;">Bonjour {{ $userName }},</h2>
                <p style="margin: 0 0 16px; color: #4b5563; font-size: 16px; line-height: 1.5;">
                    Merci de vous être inscrit sur Mini-Rb. Pour activer votre compte et commencer à explorer des logements au Maroc, veuillez cliquer sur le bouton ci-dessous :
                </p>

                <p style="text-align: center; margin: 32px 0;">
                    <a href="{{ $verificationUrl }}"
                       style="display: inline-block; background: #f43f5e; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 10px; font-weight: bold; font-size: 16px;">
                        Vérifier mon email
                    </a>
                </p>

                <p style="margin: 0 0 8px; color: #6b7280; font-size: 13px; line-height: 1.5;">
                    Ce lien expire dans 60 minutes. Si le bouton ne fonctionne pas, copiez-collez l'URL suivante dans votre navigateur :
                </p>
                <p style="margin: 0 0 24px; color: #9ca3af; font-size: 12px; word-break: break-all;">
                    {{ $verificationUrl }}
                </p>

                <p style="margin: 0; color: #9ca3af; font-size: 13px; border-top: 1px solid #e5e7eb; padding-top: 20px;">
                    Si vous n'avez pas créé de compte sur Mini-Rb, vous pouvez ignorer ce message en toute sécurité.
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding: 24px 40px; background: #f9fafb; text-align: center; color: #9ca3af; font-size: 12px;">
                &copy; 2026 Mini-Rb · Mini-Rb, by Imane &amp; Naima.
            </td>
        </tr>
    </table>
</body>
</html>
