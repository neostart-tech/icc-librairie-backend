@extends('emails.layout', ['headerTitle' => 'Commande Traitée - Notification Admin'])

@section('content')
    <h2>Bonjour Administrateur,</h2>
    
    <p>La commande <strong>#{{ $commande->reference }}</strong> a été marquée comme traitée.</p>
    
    <div style="background-color: #f8fafc; border-left: 4px solid #6a0d5f; padding: 20px; margin: 25px 0;">
        <p style="margin: 5px 0;"><strong>Référence :</strong> #{{ $commande->reference }}</p>
        <p style="margin: 5px 0;"><strong>Client :</strong> {{ $commande->user->nom }} {{ $commande->user->prenom }}</p>
        <p style="margin: 5px 0;"><strong>Statut :</strong> Traitée</p>
        <p style="margin: 5px 0;"><strong>Date de traitement :</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <p>Cette commande est désormais clôturée dans le système.</p>

    <div style="text-align: center; margin-top: 40px;">
        <a href="{{ env('DASHBOARD_URL') }}/commandes" class="btn">Voir toutes les commandes</a>
    </div>
@endsection
