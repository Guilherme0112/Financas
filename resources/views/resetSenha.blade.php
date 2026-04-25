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
        .info-box {
            background-color: #f0fdf4;
            border-left: 4px solid #22c55e;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #166534;
        }
        .footer { font-size: 12px; color: #94a3b8; text-align: center; margin-top: 40px; border-top: 1px solid #f1f5f9; padding-top: 20px; }
        .break-all { word-break: break-all; color: #94a3b8; font-size: 11px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="welcome-title">{{ config('app.name') }}</h1>
            <p style="color: #64748b; margin-top: -10px;">Recuperação de Acesso</p>
        </div>
        
        <p>Olá,</p>
        
        <p>Você está recebendo este e-mail porque recebemos uma solicitação de <strong>redefinição de senha</strong> para sua conta no <span class="highlight">{{ config('app.name') }}</span>.</p>
        
        <div class="info-box">
            <strong>Segurança em primeiro lugar:</strong> Se você não solicitou essa alteração, nenhuma ação adicional é necessária. Sua senha permanecerá a mesma e sua conta continua segura.
        </div>

        <div style="text-align: center; margin: 35px 0;">
            <a href="{{ $url }}" class="button">Redefinir Minha Senha</a>
        </div>

        <p>Este link de redefinição de senha expirará em <strong>{{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutos</strong>.</p>

        <p>Após clicar no botão, você será direcionado para uma página segura onde poderá escolher sua nova credencial de acesso.</p>

        <p>Atenciosamente,<br><strong>Equipe {{ config('app.name') }}</strong></p>

        <div class="footer">
            <p>Se você estiver tendo problemas para clicar no botão "Redefinir Minha Senha", copie e cole a URL abaixo no seu navegador:</p>
            <p class="break-all">{{ $url }}</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }} - Gestão Inteligente de Assinaturas.</p>
        </div>
    </div>
</body>
</html>