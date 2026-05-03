@extends('emails.layout')

@section('content')
<div style="padding: 20px;">
    <h2 style="color: #dc3545;">Problème avec votre paiement</h2>
    <p>Bonjour {{ $commande->user->prenom }},</p>
    <p>Nous n'avons pas pu valider votre déclaration de paiement pour la commande <strong>#{{ $commande->reference }}</strong>.</p>
    
    <div style="background-color: #fff3f3; padding: 15px; border-radius: 10px; border-left: 5px solid #dc3545; margin: 20px 0;">
        <p style="margin: 0;"><strong>Motif du refus :</strong></p>
        <p style="margin: 10px 0 0 0; font-style: italic;">"{{ $motif }}"</p>
    </div>

    <p>Veuillez vous connecter à votre espace client pour soumettre une nouvelle déclaration avec les informations correctes ou contacter notre support.</p>
    
    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ config('app.client_url') }}/dashboard/commandes" style="background-color: #6a0d5f; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">Mes Commandes</a>
    </div>
</div>
@endsection
