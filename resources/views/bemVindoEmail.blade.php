<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155; margin: 0; padding: 0; background-color: #f8fafc; }
        .container { max-width: 600px; margin: 20px auto; padding: 30px; border: 1px solid #e2e8f0; border-radius: 12px; background-color: #ffffff; }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 2px solid #f0fdf4; }
        .welcome-title { color: #15803d; margin-top: 10px; }
        .highlight { color: #16a34a; font-weight: bold; }
        .button { 
            display: inline-block; 
            padding: 14px 30px; 
            background-color: #22c55e; 
            color: #ffffff !important; 
            text-decoration: none; 
            border-radius: 8px; 
            font-weight: bold;
            box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.2);
        }
        ul { padding-left: 20px; }
        li { margin-bottom: 10px; }
        li::marker { color: #22c55e; }
        .footer { font-size: 12px; color: #94a3b8; text-align: center; margin-top: 40px; border-top: 1px solid #f1f5f9; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="welcome-title">Bem-vindo ao {{ config('app.name') }}!</h1>
        </div>
        
        <p>Olá, <strong>{{ $user->name }}</strong>,</p>
        
        <p>É um prazer ter você conosco! O <span class="highlight">{{ config('app.name') }}</span> foi criado para transformar sua relação com o dinheiro, trazendo clareza e automação para sua rotina financeira.</p>
        
        <p><strong>O que você pode fazer agora para começar:</strong></p>
        <ul>
            <li>Configurar suas <strong>metas financeiras</strong> no nosso simulador inteligente.</li>
            <li>Conectar suas principais <strong>faturas e assinaturas</strong> para nunca mais esquecer um vencimento.</li>
            <li>Visualizar seu <strong>fluxo de caixa</strong> detalhado no dashboard.</li>
        </ul>

        <div style="text-align: center; margin: 35px 0;">
            <a href="{{ config('app.url') }}/dashboard" class="button">Acessar Meu Painel</a>
        </div>

        <p>Se precisar de qualquer suporte técnico ou financeiro, basta responder a este e-mail. Nossa equipe está pronta para te ajudar!</p>

        <p>Bons investimentos,<br><strong>Equipe {{ config('app.name') }}</strong></p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }} - Gestão Inteligente de Assinaturas.<br>
            Você recebeu este e-mail porque se cadastrou em nossa plataforma.</p>
        </div>
    </div>
</body>
</html>