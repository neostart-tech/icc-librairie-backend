@extends('emails.layout', ['headerTitle' => 'Votre Commande vous a été servie !'])

@section('content')
    <h2>Bonjour {{ $commande->user->prenom }},</h2>
    
    <p>Nous avons le plaisir de vous informer que votre commande <strong>#{{ $commande->reference }}</strong> a été traitée et vous a bien été servie.</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <div class="badge badge-success" style="font-size: 16px; padding: 10px 25px;">Statut : Traitée / Livrée</div>
    </div>

    <p>Nous espérons que vos nouveaux livres vous apporteront entière satisfaction.</p>
    
    <h3>Récapitulatif final</h3>
    <table class="order-details">
        <thead>
            <tr>
                <th>Article</th>
                <th style="text-align: center;">Qté</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->detailcommandes as $detail)
            <tr>
                <td>{{ $detail->livre->titre }}</td>
                <td style="text-align: center;">{{ $detail->quantite }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p>Merci d'avoir choisi <strong>ICC Librairie</strong> !</p>

    <div style="text-align: center; margin-top: 40px;">
        <a href="{{ env('FRONTEND_URL') }}" class="btn">Retour à la boutique</a>
    </div>
@endsection
