@extends('emails.layout')

@section('content')
<div style="padding: 20px;">
    <h2 style="color: #6a0d5f;">Déclaration de paiement reçue</h2>
    <p>Bonjour {{ $commande->user->prenom }},</p>
    <p>Nous avons bien reçu votre déclaration de paiement pour la commande <strong>#{{ $commande->reference }}</strong>.</p>
    <p>Notre équipe administrative va procéder à la vérification de votre paiement dans les plus brefs délais.</p>
    
    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 10px; margin: 20px 0;">
        <p style="margin: 0;"><strong>Référence de transaction :</strong> {{ $commande->reference_paiement_client }}</p>
        <p style="margin: 5px 0 0 0;"><strong>Montant total :</strong> {{ number_format($commande->prix_total + $commande->frais_livraison, 0, ',', ' ') }} FCFA</p>
    </div>

    <p>Vous recevrez un nouvel email dès que votre paiement aura été validé.</p>
    <p>Merci de votre confiance !</p>
</div>
@endsection
