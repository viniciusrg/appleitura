<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Redefinição de Senha</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            padding: 20px;
            text-align: center;
        }
        .token {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Redefinição de Senha</h1>
        <p>Olá,</p>
        <p>Você solicitou a redefinição de sua senha. Por favor, use o token abaixo para redefinir sua senha no aplicativo:</p>
        <div class="token">{{ $data['token'] }}</div>
        <p>Se você não solicitou a redefinição de senha, por favor, ignore este email.</p>
        <p>Obrigado,</p>
        <p>A Equipe</p>
    </div>
</body>
</html>