@extends('emails.layout', ['headerTitle' => 'Nouvelle Commande Reçue'])

@section('content')
    <h2>Bonjour Administrateur,</h2>
    
    <p>Une nouvelle commande vient d'être payée sur la plateforme <strong>ICC Librairie</strong>.</p>
    
    <div style="background-color: #f8fafc; border-left: 4px solid #6a0d5f; padding: 20px; margin: 25px 0;">
        <p style="margin: 5px 0;"><strong>Référence :</strong> #{{ $commande->reference }}</p>
        <p style="margin: 5px 0;"><strong>Client :</strong> {{ $commande->user->nom }} {{ $commande->user->prenom }}</p>
        <p style="margin: 5px 0;"><strong>Email :</strong> {{ $commande->user->email }}</p>
        <p style="margin: 5px 0;"><strong>Téléphone :</strong> {{ $commande->user->telephone ?? 'Non renseigné' }}</p>
        <p style="margin: 5px 0;"><strong>Montant :</strong> {{ number_format($commande->prix_total, 0, ',', ' ') }} FCFA</p>
    </div>

    <h3>Détails des articles</h3>
    
    <table class="order-details">
        <thead>
            <tr>
                <th>Livre</th>
                <th style="text-align: center;">Quantité</th>
                <th style="text-align: right;">Prix Unitaire</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->detailcommandes as $detail)
            <tr>
                <td>{{ $detail->livre->titre }}</td>
                <td style="text-align: center;">{{ $detail->quantite }}</td>
                <td style="text-align: right;">{{ number_format($detail->prix_unitaire, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="text-align: center; margin-top: 40px;">
        <a href="{{ env('DASHBOARD_URL') }}/commandes/{{ $commande->id }}" class="btn">Gérer la commande</a>
    </div>
@endsection
