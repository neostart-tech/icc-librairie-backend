@extends('emails.layout', ['headerTitle' => 'Commande Confirmée !'])

@section('content')
    <h2>Bonjour {{ $commande->user->prenom }},</h2>
    
    <p>Nous avons le plaisir de vous informer que votre commande <strong>#{{ $commande->reference }}</strong> a bien été enregistrée et payée avec succès.</p>
    
    <p>Notre équipe prépare actuellement vos articles. <strong>Un membre de notre équipe vous contactera très prochainement</strong> pour organiser la livraison ou le retrait de vos livres.</p>

    <h3>Récapitulatif de votre commande</h3>
    
    <table class="order-details">
        <thead>
            <tr>
                <th>Livre</th>
                <th style="text-align: center;">Qté</th>
                <th style="text-align: right;">Prix</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->detailcommandes as $detail)
            <tr>
                <td>{{ $detail->livre->titre }}</td>
                <td style="text-align: center;">{{ $detail->quantite }}</td>
                <td style="text-align: right;">{{ number_format($detail->prix_unitaire * $detail->quantite, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" style="text-align: right; padding-top: 20px;">TOTAL</td>
                <td style="text-align: right; padding-top: 20px;">{{ number_format($commande->prix_total, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tbody>
    </table>

    <div style="text-align: center; margin-top: 40px;">
        <p>Merci pour votre confiance !</p>
        <a href="{{ env('FRONTEND_URL') }}/dashboard" class="btn">Suivre ma commande</a>
    </div>
@endsection
