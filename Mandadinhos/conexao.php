<?php
$servidor = 'localhost';
$banco = 'streparavadb';
$usuario = 'root';
$senha = '';

try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$banco; charset=utf8", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro de conexão ao Banco de Dados:' . $e->getMessage();
}

// Só processa o login se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userP'], $_POST['pswP'])) {
    session_start(); // Inicia a sessão aqui!
    $usuario = $_POST['userP'];
    $senha = md5($_POST['pswP']); // Criptografa a senha digitada

    // $query = $pdo->query("SELECT * FROM usuarios WHERE nomeUser='$usuario' AND senhaUser='$senha'");
    // $result = $query->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        $_SESSION['user'] = $usuario; // Salva o usuário na sessão
        header("Location: principal.php");
        exit;
    } else {
        echo 'Usuario ou senha estão incorretos.';
        echo '<script>window.alert("Usuario ou senha incorreto.")</script>';
        echo '<script>window.location="index.php"</script>';
    }
}
