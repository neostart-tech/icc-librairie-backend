@extends('emails.layout')

@section('content')
<div style="padding: 20px;">
    <h2 style="color: #6a0d5f; margin-top: 0;">Demande de devis bien reçue !</h2>

    <p>Bonjour <strong>{{ $devis->nom_complet }}</strong>,</p>

    <p>Nous avons bien enregistré votre demande de devis pour le livre ci-dessous. Notre équipe vous contactera très prochainement pour vous communiquer un prix et les modalités de commande.</p>

    <div style="background-color: #f8f5f8; border-left: 4px solid #6a0d5f; border-radius: 8px; padding: 20px; margin: 24px 0;">
        <p style="margin: 0 0 8px 0; font-size: 13px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">Livre concerné</p>
        <p style="margin: 0; font-size: 18px; font-weight: 700; color: #1e293b;">{{ $devis->livre->titre ?? 'Livre sur commande' }}</p>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <tr>
            <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #64748b; font-weight: 600; width: 50%;">Nom complet</td>
            <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #1e293b; font-weight: 700;">{{ $devis->nom_complet }}</td>
        </tr>
        <tr>
            <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #64748b; font-weight: 600;">Téléphone</td>
            <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #1e293b; font-weight: 700;">{{ $devis->telephone }}</td>
        </tr>
        <tr>
            <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #64748b; font-weight: 600;">Nombre d'exemplaires</td>
            <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #1e293b; font-weight: 700;">{{ $devis->nombre_exemplaires }}</td>
        </tr>
        @if($devis->message)
        <tr>
            <td style="padding: 10px 12px; font-size: 14px; color: #64748b; font-weight: 600;">Message</td>
            <td style="padding: 10px 12px; font-size: 14px; color: #1e293b;">{{ $devis->message }}</td>
        </tr>
        @endif
    </table>

    <div style="background-color: #fff8f0; border-left: 4px solid #f97316; border-radius: 8px; padding: 16px; margin: 20px 0;">
        <p style="margin: 0; font-size: 13px; color: #9a3412;">
            <strong>Que se passe-t-il ensuite ?</strong><br>
            Notre équipe analyse votre demande et reviendra vers vous dans les plus brefs délais par téléphone ou par email pour vous proposer un devis personnalisé.
        </p>
    </div>

    <p style="margin-top: 24px;">Merci de votre confiance,<br><strong>L'équipe ICC Librairie</strong></p>
</div>
@endsection
