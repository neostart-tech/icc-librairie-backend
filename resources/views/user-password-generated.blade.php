<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre nouveau compte</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .welcome-email {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background-color: #ffffff;
        }

        .welcome-email h1 {
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .welcome-email p {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .welcome-email a {
            color: #0d6efd;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }

        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="welcome-email">
        <img src="{{ asset('logo-icc.jpg') }}" alt="Logo ICC" class="logo">
        <h1>Bonjour, Monsieur/Madame {{ $user->nom }} {{ $user->prenom }}</h1>
        <p>Soyez le/la bienvenu(e) au sein de notre équipe. Votre compte a été créé avec succès et voici vos
            identifiants de connexion :</p>
        <div class="mb-3">
            <p><strong>Email :</strong> {{ $user->email }}</p>
            <p><strong>Mot de passe :</strong> {{ $generatedPassword }}</p>
        </div>
        <p>Veuillez vous connecter en cliquant sur le lien ci-après : <a href="{{ env('FRONTEND_URL') }}">Se
                connecter</a></p>
        <div class="footer">
            <p>Merci de faire partie de notre communauté !</p>
            <p>Si vous avez des questions, n'hésitez pas à nous approcher.</p>
        </div>
    </div>
</body>

</html>
