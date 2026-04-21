@extends('emails.layout', ['headerTitle' => 'Bienvenue sur ICC Librairie'])

@section('content')
    <h2>Bonjour {{ $user->prenom }} {{ $user->nom }},</h2>
    
    <p>Nous avons le plaisir de vous informer que votre compte a été créé avec succès sur la plateforme <strong>ICC Librairie</strong>.</p>
    
    <div style="background-color: #f8fafc; border-left: 4px solid #6a0d5f; padding: 20px; margin: 25px 0;">
        <p style="margin: 5px 0;"><strong>Email :</strong> {{ $user->email }}</p>
        <p style="margin: 5px 0;"><strong>Mot de passe temporaire :</strong> <span style="font-family: monospace; font-size: 18px; color: #6a0d5f; background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">{{ $generatedPassword }}</span></p>
    </div>

    <p>Pour des raisons de sécurité, nous vous conseillons de changer votre mot de passe dès votre première connexion.</p>

    <div style="text-align: center; margin-top: 40px;">
        <a href="{{ env('DASHBOARD_URL') }}" class="btn">Me connecter</a>
    </div>

    <p style="margin-top: 30px;">Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email.</p>
@endsection
