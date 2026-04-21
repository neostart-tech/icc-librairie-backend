<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de votre mot de passe</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 30px;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .logo {
            max-width: 120px;
            margin-bottom: 25px;
            display: block;
        }
        h1 {
            font-size: 22px;
            color: #1a1a1a;
            margin-bottom: 20px;
            font-weight: 600;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            color: #4a4a4a;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #f97316; /* Orange color */
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 10px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 13px;
            color: #888;
        }
        .note {
            font-size: 14px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="email-container">
        @if(file_exists(public_path('logo-icc.jpg')))
            <img src="{{ $message->embed(public_path('logo-icc.jpg')) }}" alt="Logo ICC" class="logo">
        @endif
        
        <h1>Bonjour {{ $user->prenom }},</h1>
        
        <p>Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte sur <strong>ICC Librairie</strong>.</p>
        
        <p>Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe :</p>
        
        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="btn">Réinitialiser le mot de passe</a>
        </div>
        
        <p>Ce lien de réinitialisation expirera dans {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutes.</p>
        
        <p>Si vous n'avez pas demandé de réinitialisation de mot de passe, aucune action supplémentaire n'est requise.</p>
        
        <p class="note">Si vous avez des difficultés à cliquer sur le bouton "Réinitialiser le mot de passe", copiez et collez l'URL ci-dessous dans votre navigateur :<br>
        <span style="word-break: break-all; color: #f97316;">{{ $resetUrl }}</span></p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} ICC Librairie. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
