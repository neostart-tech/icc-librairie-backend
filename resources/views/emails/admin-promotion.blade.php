@extends('emails.layout', ['headerTitle' => 'Félicitations pour votre promotion !'])

@section('content')
    <h2>Bonjour {{ $user->prenom }} {{ $user->nom }},</h2>
    
    <p>Nous avons le plaisir de vous informer que votre compte a été promu au rang d'<strong>Administrateur</strong> sur la plateforme <strong>ICC Librairie</strong>.</p>
    
    <p>Ce nouveau rôle vous donne accès au tableau de bord pour gérer :</p>
    <ul>
        <li>Les livres et le catalogue</li>
        <li>Les commandes et les paiements</li>
        <li>Les stocks et les mouvements</li>
        <li>Les catégories et les auteurs</li>
    </ul>

    <div style="background-color: #f8fafc; border-left: 4px solid #6a0d5f; padding: 20px; margin: 25px 0;">
        <p style="margin: 5px 0;"><strong>Niveau d'accès :</strong> Administrateur</p>
        <p style="margin: 5px 0;"><strong>Email :</strong> {{ $user->email }}</p>
    </div>

    <p>Vous pouvez désormais vous connecter à votre espace d'administration en utilisant vos identifiants habituels.</p>

    <div style="text-align: center; margin-top: 40px;">
        <a href="{{ env('DASHBOARD_URL') }}" class="btn">Accéder au Dashboard</a>
    </div>

    <p style="margin-top: 30px;">Si vous avez des questions concernant vos nouvelles responsabilités, n'hésitez pas à contacter le super-administrateur.</p>
@endsection
