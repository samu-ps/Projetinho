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
    $nomeUser = $_POST['userP'];
    $senhaUser = md5($_POST['pswP']); // Criptografa a senha digitada

    $stmt = $pdo->prepare("SELECT * FROM streparavadb.usuario WHERE login = :login AND senha = :senha");
    $stmt->bindParam(':login', $nomeUser);
    $stmt->bindParam(':senha', $senhaUser);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            $_SESSION['user'] = $nomeUser;
            header("Location: principal.php");
            exit;
        } else {
            echo '<script>alert("Usuario ou senha incorreto.")</script>';
            echo '<script>window.location="index.php"</script>';
    }
}
