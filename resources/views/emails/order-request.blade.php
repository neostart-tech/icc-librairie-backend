@extends('emails.layout')

@section('content')
<div style="padding: 20px;">
    <h2 style="color: #6a0d5f;">Votre demande de commande est enregistrée</h2>
    <p>Bonjour {{ $commande->user->prenom }},</p>
    <p>Nous avons bien enregistré votre demande de commande <strong>#{{ $commande->reference }}</strong>.</p>
    
    <div style="background-color: #fff8f0; padding: 15px; border-radius: 10px; border-left: 5px solid #ffa500; margin: 20px 0;">
        <p style="margin: 0;"><strong>Statut :</strong> En attente de paiement</p>
        <p style="margin: 5px 0 0 0;"><strong>Montant à régler :</strong> {{ number_format($commande->prix_total + $commande->frais_livraison, 0, ',', ' ') }} FCFA</p>
    </div>

    <p>Pour valider votre commande, veuillez suivre les instructions de paiement affichées sur le site et déclarer votre paiement dans votre espace client.</p>
    
    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ config('app.client_url') }}/dashboard/commandes" style="background-color: #6a0d5f; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">Voir ma commande</a>
    </div>

    <p>À très bientôt sur ICC Librairie !</p>
</div>
@endsection
