@extends('emails.layout')

@section('content')
<div style="padding: 20px;">
    <h2 style="color: #6a0d5f;">Nouveau paiement à valider</h2>
    <p>Bonjour Admin,</p>
    <p>Le client <strong>{{ $commande->user->nom }} {{ $commande->user->prenom }}</strong> vient de déclarer un paiement pour la commande <strong>#{{ $commande->reference }}</strong>.</p>
    
    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 10px; margin: 20px 0;">
        <p style="margin: 0;"><strong>Référence client :</strong> {{ $commande->reference_paiement_client }}</p>
        <p style="margin: 5px 0 0 0;"><strong>Montant attendu :</strong> {{ number_format($commande->prix_total + $commande->frais_livraison, 0, ',', ' ') }} FCFA</p>
    </div>

    <p>Veuillez vous connecter au tableau de bord pour vérifier la preuve de paiement et valider la commande.</p>
    
    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ config('app.dashboard_url') }}/commandes" style="background-color: #6a0d5f; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">Accéder au Dashboard</a>
    </div>
</div>
@endsection
