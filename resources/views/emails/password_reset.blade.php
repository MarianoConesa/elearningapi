<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tu nueva contraseña</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 30px; background-color: #F9FAFB; color: #2E3A59;">
    <div style="max-width: 600px; margin: auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <h2 style="color: #1E88E5;">Hola {{ $user->name ?? 'usuario' }},</h2>

        <p>Hemos generado una nueva contraseña para tu cuenta:</p>

        <p style="font-size: 20px; font-weight: bold; background-color: #f0f4ff; padding: 12px 20px; border-radius: 6px; display: inline-block;">
            {{ $newPassword }}
        </p>

        <p style="margin-top: 20px;">
            Te recomendamos iniciar sesión lo antes posible y cambiarla por una que recuerdes fácilmente desde tu perfil.
        </p>

        <p>Si tú no solicitaste este cambio, por favor contáctanos de inmediato.</p>

        <br>
        <p>Saludos,<br>El equipo de Elearning</p>
    </div>
</body>
</html>
