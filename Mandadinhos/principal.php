<?php
// session_start();
// if (!isset($_SESSION['user'])) {
//     header("Location: index.php");
//     exit();
// }

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./img/FAVICON.png" type="image/png">
    <link rel="stylesheet" href="./css/principal.css">
    <title>Página Principal</title>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="opcoes">
            <button class="btnopcoes" id="btn3">Presset</button>
            <div class="dropdown-armario">
                <button class="btnopcoes" id="btnEx">Armários ⇩</button>
                <div class="dropdown-content" id="armarioDropdown" style="display: none;">
                    <button class="dropdown-item" data-page="armarios/armario1.php">Linha 1</button>
                    <button class="dropdown-item" data-page="armarios/armario2.php">Linha 2</button>
                    <button class="dropdown-item" data-page="armarios/armario3.php">Linha 3</button>
                    <button class="dropdown-item" data-page="armarios/armario4.php">Linha 4</button>
                    <button class="dropdown-item" data-page="armarios/armario5.php">Linha 5</button>
                    <button class="dropdown-item" data-page="armarios/armario6.php">Linha 6</button>
                </div>
            </div>
            <button class="btnopcoes" id="btn2">Relatório</button>
            <button class="btnopcoes" id="btn4">Cadastrar Ferramenta</button>
            <button class="btnopcoes" id="btn10">Sobre</button>
        </div>
        <div id="conteudo">
        </div>
    </div>
    <div id="modalErro" class="modal" style="display:none;">
        <div class="modal-content" style="text-align:center;">
            <h3>Erro</h3>
            <p id="msgErro">Quantidade indisponível!</p>
            <button id="btnOkErro" class="btn-primary" style="margin-top:10px;">OK</button>
        </div>
    </div>
</body>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="./scripts/principal.js"></script>
<script>

</script>

</html>