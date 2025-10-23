<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinição de Senha</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            color: #ff6b35;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .title {
            color: #333;
            font-size: 22px;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #ff6b35;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .btn:hover {
            background-color: #e55a2e;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .link-box {
            word-break: break-all;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ config('app.name') }}</div>
            <h1 class="title">Redefinição de Senha</h1>
        </div>

        <div class="content">
            <p>Olá <strong>{{ $user->name }}</strong>,</p>

            <p>Recebemos uma solicitação para redefinir a senha da sua conta. Se você fez essa solicitação, clique no botão abaixo para criar uma nova senha:</p>

            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="btn">Redefinir Senha</a>
            </div>

            <p>Ou copie e cole o link abaixo no seu navegador:</p>
            <p class="link-box">{{ $resetUrl }}</p>

            <div class="warning">
                <strong>⚠️ Importante:</strong>
                <ul>
                    <li>Este link é válido por apenas <strong>24 horas</strong></li>
                    <li>Se você não solicitou esta redefinição, ignore este email</li>
                    <li>Sua senha atual permanecerá inalterada até que você use este link</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            <p>Este é um email automático, não responda.</p>
            <p>Se você continuar tendo problemas, entre em contato com o suporte.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>
