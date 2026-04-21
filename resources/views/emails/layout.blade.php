<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ICC Librairie' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            color: #1e293b;
        }
        .container {
            max-width: 650px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #6a0d5f;
            padding: 40px 30px;
            text-align: center;
        }
        .header img {
            max-width: 120px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 30px;
            text-align: center;
            font-size: 13px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .order-details {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
        }
        .order-details th {
            text-align: left;
            padding: 12px;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            font-size: 14px;
            color: #475569;
        }
        .order-details td {
            padding: 15px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 15px;
        }
        .total-row {
            font-weight: 700;
            font-size: 18px;
            color: #6a0d5f;
        }
        .btn {
            display: inline-block;
            padding: 14px 28px;
            background-color: #f97316;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            margin-top: 20px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-success { background-color: #dcfce7; color: #166534; }
        .badge-warning { background-color: #fef9c3; color: #854d0e; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(file_exists(public_path('logo-icc.jpg')))
                <img src="{{ $message->embed(public_path('logo-icc.jpg')) }}" alt="Logo ICC">
            @endif
            <h1>{{ $headerTitle ?? 'ICC Librairie' }}</h1>
        </div>
        
        <div class="content">
            @yield('content')
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} ICC Librairie. Tous droits réservés.</p>
            <p>Ce message a été envoyé automatiquement, merci de ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>
