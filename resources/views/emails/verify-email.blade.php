<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verifica tu correo</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px; background-color: #F9FAFB;">
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="{{ asset('assets/logos/logoIconWhite.svg') }}" alt="Logo Elearning" style="height: 80px;">
    </div>

    <h2>Hola {{ $user->name ?? 'usuario' }},</h2>
    <p>Gracias por registrarte. Haz clic en el botón de abajo para verificar tu correo:</p>

    <div style="margin: 20px 0;">
        <a href="{{ $url }}" style="display: inline-block; padding: 12px 24px; background-color: #1E88E5; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;">
            Verificar correo
        </a>
    </div>

    <p>Este enlace expirará en 60 minutos.</p>
    <p>Si no te registraste, puedes ignorar este mensaje.</p>

    <br>
    <p>Saludos,<br>El equipo de Elearning</p>
</body>
</html>
