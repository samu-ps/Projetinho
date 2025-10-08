<?php
session_start();

// // Verificar se a variável 'user' possui conteúdo
// if (!isset($_SESSION['user'])) {
//     header("Location: index.php");
//     exit();
// }

$usuario = $_SESSION['user'];
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- 2-Editar a estrutura padrão: titúlo,css,icone -->
    <link rel="icon" href="./img/FAVICON.png" type="image/png">
    <link rel="stylesheet" href="./css/pgPrincipal.css">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <title>Página Principal</title>
</head>

<body>
    <?php
    include 'header.php';
    ?>
    <div class="container">
        <div class="opcoes">
            <button class="dropdown" id="btnEx">ARMARIO</button>
            <button class="btnopcoes" id="btn2">SEI LA</button>


        </div>

        <div id="conteudo">

            <h2>CONTEÚDO</h2>
            <p>Aqui aparece o conteúdo ao lado do menu.</p>

        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        // Botão Inspeção
        $('#btnEx').click(function() {
            $('#conteudo').load('armario.php', function(response, status, xhr) {
                if (status == "error") {
                    $('#conteudo').html('<p>Erro ao carregar Inspeção</p>');
                }
            });
        });
    });
    $(document).ready(function() {
        // Botão Inspeção
        $('#btn2').click(function() {
            $('#conteudo').load('SeiLa.php', function(response, status, xhr) {
                if (status == "error") {
                    $('#conteudo').html('<p>Erro ao carregar Inspeção</p>');
                }
            });
        });
    });
    const usuarioBtn = document.querySelector('.usuario-btn');
    const modalSair = document.getElementById('modalSair');
    const cancelarSair = document.getElementById('cancelarSair');

    usuarioBtn.addEventListener('click', () => {
        modalSair.style.display = 'flex';
    });

    cancelarSair.addEventListener('click', () => {
        modalSair.style.display = 'none';
    });

    // Fechar modal clicando fora da caixa
    window.addEventListener('click', (e) => {
        if (e.target === modalSair) {
            modalSair.style.display = 'none';
        }
    });
</script>

</html>