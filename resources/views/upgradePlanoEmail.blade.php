<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #334155;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 30px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background-color: #ffffff;
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0fdf4;
        }

        .upgrade-title {
            color: #15803d;
            margin-top: 10px;
        }

        .badge {
            display: inline-block;
            background-color: #dcfce7;
            color: #16a34a;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .highlight {
            color: #16a34a;
            font-weight: bold;
        }

        .button {
            display: inline-block;
            padding: 14px 30px;
            background-color: #16a34a;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.2);
        }

        .feature-card {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #22c55e;
        }

        .footer {
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
            margin-top: 40px;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="badge">Upgrade Concluído</div>
            <h1 class="upgrade-title">Você agora é {{ $plano->nome }}! 🚀</h1>
        </div>

        <p>Olá, <strong>{{ $user->name }}</strong>,</p>

        <p>Seu upgrade para o plano <span class="highlight">{{ $plano->nome }}</span> no {{ config('app.name') }} foi
            confirmado.</p>

        <div class="feature-card">
            <strong>Detalhes da sua nova assinatura:</strong>
            <ul style="margin-top: 10px; padding-left: 20px;">
                <li><strong>Plano:</strong> {{ $plano->nome }}</li>
                <li><strong>Valor:</strong> R$ {{ number_format($fatura->valor, 2, ',', '.') }}</li>
                <li><strong>Próximo vencimento:</strong>
                    {{ $fatura->assinatura->data_proxima_cobranca->format('d/m/Y') }}</li>
                <li><strong>Método de pagamento:</strong> {{ $fatura->metodo_pagamento_label }}</li>
            </ul>
        </div>

        <p>Todos os recursos do plano <strong>{{ $plano->nome }}</strong> já estão liberados no seu dashboard.</p>

        <div style="text-align: center; margin: 35px 0;">
            <a href="{{ config('app.url') }}/dashboard" class="button">Acessar Meu Painel</a>
        </div>

    </div>
</body>

</html>