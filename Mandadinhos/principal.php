<?php
session_start();

// // Verificar se a variável 'user' possui conteúdo
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
            <div class="dropdown-armario">
                <button class="btnopcoes" id="btnEx">ARMARIOS  ⇩</button>
                <div class="dropdown-content" id="armarioDropdown" style="display: none;">
                    <button class="dropdown-item" data-page="armario1.php">Linha 1</button>
                    <button class="dropdown-item" data-page="armario2.php">Linha 2</button>
                    <button class="dropdown-item" data-page="armario3.php">Linha 3</button>
                    <button class="dropdown-item" data-page="armario4.php">Linha 4</button>
                    <button class="dropdown-item" data-page="armario.php">Cadastrar</button>
                </div>
            </div>
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
        // Dropdown ARMARIO
        $('#btnEx').click(function() {
            $('#armarioDropdown').slideToggle(200);
        });

        // Carregar conteúdo das opções do dropdown
        $('.dropdown-item').click(function() {
            const page = $(this).data('page');
            $('#conteudo').load(page, function(response, status, xhr) {
                if (status == "error") {
                    $('#conteudo').html('<p>Erro ao carregar conteúdo</p>');
                }
            });
        });

        // Botão SEI LA
        $('#btn2').click(function() {
            $('#conteudo').load('SeiLa.php', function(response, status, xhr) {
                if (status == "error") {
                    $('#conteudo').html('<p>Erro ao carregar Inspeção</p>');
                }
            });
        });

        // Modal usuário (mantido)
        const usuarioBtn = document.querySelector('.usuario-btn');
        const modalSair = document.getElementById('modalSair');
        const cancelarSair = document.getElementById('cancelarSair');

        usuarioBtn.addEventListener('click', () => {
            modalSair.style.display = 'flex';
        });

        cancelarSair.addEventListener('click', () => {
            modalSair.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === modalSair) {
                modalSair.style.display = 'none';
            }
        });
    });
    function carregarArmarios() {
        fetch('http://localhost:8000/armarios')
            .then(response => response.json())
            .then(data => {
                let html = '<h2>Armários</h2><ul>';
                data.forEach(armario => {
                    html += `<li>ID: ${armario.id} | Turno: ${armario.turno} | Linha: ${armario.linha}</li>`;
                });
                html += '</ul>';
                document.getElementById('conteudo').innerHTML = html;
            })
            .catch(() => {
                document.getElementById('conteudo').innerHTML = '<p>Erro ao carregar armários.</p>';
            });
        }
        function carregarArmariosPorLinha(linha) {
        fetch(`http://localhost:8000/armarios/linha/${linha}`)
            .then(response => response.json())
            .then(data => {
                let html = `<h2>Armários da Linha ${linha}</h2><ul>`;
                data.forEach(armario => {
                    html += `<li>ID: ${armario.id} | Turno: ${armario.turno} | Linha: ${armario.linha}</li>`;
                });
                html += '</ul>';
                document.getElementById('conteudo').innerHTML = html;
            })
            .catch(() => {
                document.getElementById('conteudo').innerHTML = '<p>Erro ao carregar armários.</p>';
            });
    }
    function carregarArmariosPorLinhaSeparadoPorTurno(linha) {
        fetch(`http://localhost:8000/armarios/linha/${linha}`)
            .then(response => response.json())
            .then(data => {
                let matutino = [];
                let vespertino = [];
                let noturno = [];
                data.forEach(armario => {
                    if (armario.turno.toLowerCase() === 'matutino') matutino.push(armario);
                    else if (armario.turno.toLowerCase() === 'vespertino') vespertino.push(armario);
                    else if (armario.turno.toLowerCase() === 'noturno') noturno.push(armario);
                });

                let html = `<div class="armarios-titulo">Armários da Linha ${linha}</div>`;
                html += `<div class="armario-turno matutino"><h3>Matutino</h3><ul class="armario-lista">`;
                matutino.forEach(a => html += `<li><strong>ID:</strong> ${a.id} | <strong>Linha:</strong> ${a.linha}</li>`);
                html += `</ul></div>`;
                html += `<div class="armario-turno vespertino"><h3>Vespertino</h3><ul class="armario-lista">`;
                vespertino.forEach(a => html += `<li><strong>ID:</strong> ${a.id} | <strong>Linha:</strong> ${a.linha}</li>`);
                html += `</ul></div>`;
                html += `<div class="armario-turno noturno"><h3>Noturno</h3><ul class="armario-lista">`;
                noturno.forEach(a => html += `<li><strong>ID:</strong> ${a.id} | <strong>Linha:</strong> ${a.linha}</li>`);
                html += `</ul></div>`;

                document.getElementById('conteudo').innerHTML = html;
            })
            .catch(() => {
                document.getElementById('conteudo').innerHTML = '<p>Erro ao carregar armários.</p>';
            });
    }

    // Substitua os eventos dos botões do dropdown:
    document.querySelector('[data-page="armario1.php"]').addEventListener('click', function(e) {
        e.preventDefault();
        carregarArmariosPorLinhaSeparadoPorTurno('1');
    });
    document.querySelector('[data-page="armario2.php"]').addEventListener('click', function(e) {
        e.preventDefault();
        carregarArmariosPorLinhaSeparadoPorTurno('2');
    });
    document.querySelector('[data-page="armario3.php"]').addEventListener('click', function(e) {
        e.preventDefault();
        carregarArmariosPorLinhaSeparadoPorTurno('3');
    });
    document.querySelector('[data-page="armario4.php"]').addEventListener('click', function(e) {
        e.preventDefault();
        carregarArmariosPorLinhaSeparadoPorTurno('4');
    });
</script>

</html>