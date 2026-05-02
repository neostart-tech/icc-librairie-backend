@extends('emails.layout')

@section('content')
<div style="padding: 20px;">
    <h2 style="color: #28a745;">Paiement validé !</h2>
    <p>Bonjour {{ $commande->user->prenom }},</p>
    <p>Bonne nouvelle ! Votre paiement pour la commande <strong>#{{ $commande->reference }}</strong> a été validé par notre équipe.</p>
    
    <p>Votre commande est désormais en cours de traitement.</p>

    @if($commande->type_livraison === 'livraison')
        <p>Notre service de livraison vous contactera prochainement à l'adresse suivante :<br>
        <strong>{{ $commande->adresse_livraison }}</strong> ({{ $commande->numero_livraison }})</p>
    @else
        <p>Vous pouvez désormais passer récupérer vos ouvrages à la librairie ICC.</p>
    @endif

    <p>Merci de votre achat !</p>
</div>
@endsection
