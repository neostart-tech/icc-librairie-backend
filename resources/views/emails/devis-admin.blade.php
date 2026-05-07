@extends('emails.layout')

@section('content')
<div style="padding: 20px;">
    <h2 style="color: #6a0d5f; margin-top: 0;">Nouvelle demande de devis reçue</h2>

    <p>Une nouvelle demande de devis vient d'être soumise sur ICC Librairie.</p>

    <div style="background-color: #f8f5f8; border-left: 4px solid #6a0d5f; border-radius: 8px; padding: 20px; margin: 24px 0;">
        <p style="margin: 0 0 8px 0; font-size: 13px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">Livre concerné</p>
        <p style="margin: 0; font-size: 18px; font-weight: 700; color: #1e293b;">{{ $devis->livre->titre ?? 'Livre sur commande' }}</p>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <tr>
            <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #64748b; font-weight: 600; width: 40%;">Nom complet</td>
            <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #1e293b; font-weight: 700;">{{ $devis->nom_complet }}</td>
        </tr>
        <tr>
            <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #64748b; font-weight: 600;">Téléphone</td>
            <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #1e293b; font-weight: 700;">{{ $devis->telephone }}</td>
        </tr>
        <tr>
            <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #64748b; font-weight: 600;">Email</td>
            <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #1e293b;">{{ $devis->email ?? '—' }}</td>
        </tr>
        <tr>
            <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #64748b; font-weight: 600;">Nombre d'exemplaires</td>
            <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #1e293b; font-weight: 700;">{{ $devis->nombre_exemplaires }}</td>
        </tr>
        @if($devis->message)
        <tr>
            <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #64748b; font-weight: 600;">Message</td>
            <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #1e293b;">{{ $devis->message }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding: 10px 12px; font-size: 14px; color: #64748b; font-weight: 600;">Date de la demande</td>
            <td style="padding: 10px 12px; font-size: 14px; color: #1e293b;">{{ $devis->created_at->format('d/m/Y à H:i') }}</td>
        </tr>
    </table>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ config('app.dashboard_url', config('app.url')) }}/devis" class="btn">
            Voir dans le dashboard
        </a>
    </div>
</div>
@endsection
