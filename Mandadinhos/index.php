<!-- Estrutura php -->
<?php require_once('./conexao.php'); 
// Criar uma sessão para compartilhar as variaveis de memoria @session_start(); ?>
<!-- Estrutura html -->
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- 2-Editar a estrutura padrão: titúlo,css,icone -->
    <link rel="icon" href="./img/FAVICON.png" type="image/x-icon">
    <link rel="stylesheet" href=".\css\index.css">
    <title>Login</title>
</head>

<body>

    <div class="header">
        <img src="./img/Streparava.png" alt="logo streparava">
    </div>
    
    <section class="container">
        <div class="login-container">
            <div class="form-container">
                <img src="./img/S.png" alt="Streparava" class="imagem" />
                <h1 class="texto">LOGIN</h1>
                <form id="loginForm" action="" method="POST">
                    <input type="text" name="userP" placeholder="USUÁRIO" required>
                    <input type="password" name="pswP" placeholder="*****" required>
                    <button onclick="" class="submit-btn">Entrar</button>
                </form>
                <div class="register-forget opacity">
                </div>
            </div>
            <div class="circulo circulo-2"></div>
        </div>

    </section>
</body>

</html>