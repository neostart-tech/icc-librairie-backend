@extends('emails.layout')

@section('content')
<div style="padding: 20px;">
    <h2 style="color: #6a0d5f;">Reçu de votre achat au comptoir</h2>
    <p>Bonjour {{ $commande->nom_client }},</p>
    <p>Merci pour votre achat chez <strong>ICC Librairie</strong>. Nous avons bien enregistré votre transaction sous la référence <strong>#{{ $commande->reference }}</strong>.</p>
    
    <div style="background-color: #f0fdf4; padding: 15px; border-radius: 10px; border-left: 5px solid #22c55e; margin: 20px 0;">
        <p style="margin: 0; color: #166534;"><strong>Statut :</strong> Payé et Livré (Vente au comptoir)</p>
        <p style="margin: 5px 0 0 0; color: #166534;"><strong>Montant total :</strong> {{ number_format($commande->prix_total, 0, ',', ' ') }} FCFA</p>
    </div>

    <h3 style="color: #6a0d5f; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; margin-top: 30px;">Détails de vos articles</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
        <thead>
            <tr style="background-color: #f8fafc;">
                <th style="text-align: left; padding: 10px; font-size: 12px; color: #64748b; text-transform: uppercase;">Article</th>
                <th style="text-align: center; padding: 10px; font-size: 12px; color: #64748b; text-transform: uppercase;">Qté</th>
                <th style="text-align: right; padding: 10px; font-size: 12px; color: #64748b; text-transform: uppercase;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->detailcommandes as $detail)
            <tr>
                <td style="padding: 12px 10px; border-bottom: 1px solid #f1f5f9; font-size: 14px;">
                    <strong>{{ $detail->livre->titre }}</strong>
                </td>
                <td style="padding: 12px 10px; border-bottom: 1px solid #f1f5f9; text-align: center; font-size: 14px;">
                    {{ $detail->quantite }}
                </td>
                <td style="padding: 12px 10px; border-bottom: 1px solid #f1f5f9; text-align: right; font-size: 14px;">
                    {{ number_format($detail->prix_unitaire * $detail->quantite, 0, ',', ' ') }} FCFA
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="padding: 20px 10px 10px 10px; text-align: right; font-weight: bold; font-size: 16px; color: #6a0d5f;">Total Payé :</td>
                <td style="padding: 20px 10px 10px 10px; text-align: right; font-weight: bold; font-size: 16px; color: #6a0d5f;">{{ number_format($commande->prix_total, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tfoot>
    </table>

    <p style="margin-top: 40px;">Nous espérons que ces ouvrages vous seront en bénédiction.</p>
    
    <p>À très bientôt sur ICC Librairie !</p>
</div>
@endsection
