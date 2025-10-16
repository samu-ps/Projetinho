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
            <button class="btnopcoes" id="btn3">Presset</button>
            <div class="dropdown-armario">
                <button class="btnopcoes" id="btnEx">ARMARIOS ⇩</button>
                <div class="dropdown-content" id="armarioDropdown" style="display: none;">
                    <button class="dropdown-item" data-page="armarios/armario1.php">Linha 1</button>
                    <button class="dropdown-item" data-page="armarios/armario2.php">Linha 2</button>
                    <button class="dropdown-item" data-page="armarios/armario3.php">Linha 3</button>
                    <button class="dropdown-item" data-page="armarios/armario4.php">Linha 4</button>
                    <button class="dropdown-item" data-page="armarios/armario5.php">Linha 5</button>
                    <button class="dropdown-item" data-page="armarios/armario6.php">Linha 6</button>
                    <button class="dropdown-item" data-page="cadastros.php">Cadastrar</button>
                </div>
            </div>
            <button class="btnopcoes" id="btn2">Relatório</button>
        </div>
        <div id="conteudo">

            <h2>CONTEÚDO</h2>
            <p>Aqui aparece o conteúdo ao lado do menu.</p>

        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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

        // Botão Relatório
        $('#btn2').click(function() {
            $('#conteudo').load('relatorio.php', function(response, status, xhr) {
                if (status == "error") {
                    $('#conteudo').html('<p>Erro ao carregar Relatório</p>');
                }
            });
        });
        //Botão Presset
        $('#btn3').click(function() {
            $('#conteudo').load('presset.php', function(response, status, xhr) {
                if (status == "error") {
                    $('#conteudo').html('<p>Erro ao carregar Presset</p>');
                    // carregar(); // Recarrega os dados do backend
                }
            });
        });


        // Modal usuário (mantid)
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
        fetch('http://localhost:8080/armarios')
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
        fetch(`http://localhost:8080/armarios/linha/${linha}`)
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
        fetch(`http://localhost:8080/armarios/linha/${linha}`)
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
                html += `
                <div class="armario-turno matutino"><h3>Matutino</h3><ul class="armario-lista">
                <div class="muted" style="display:flex; gap:8px; justify-content:flex-end; align-items:center;">Ajustar peça (adicionar/retirar)</div>
                <div style="display:flex; gap:8px; justify-content:flex-end; align-items:center;">
                    <div style="display:flex;gap:8px">
                        <select id="presset-select" style="flex:1"></select>
                        <input id="presset-qty" type="number" value="1" min="1" style="width:90px" />
                        <button class="btn-primary" id="presset-add">ADICIONAR</button>
                        <button class="btn-danger" id="presset-remove">RETIRAR</button>
                    </div>
                </div>`;
                matutino.forEach(a => html += `<li><strong>ID:</strong> ${a.id} | <strong>Linha:</strong> ${a.linha}</li>`);
                html += `</ul></div>`;
                html += `<div class="armario-turno vespertino"><h3>Vespertino</h3><ul class="armario-lista">
                <div class="muted" style="display:flex; gap:8px; justify-content:flex-end; align-items:center;">Ajustar peça (adicionar/retirar)</div>
                <div style="display:flex; gap:8px; justify-content:flex-end; align-items:center;">
                    <div style="display:flex;gap:8px">
                        <select id="presset-select" style="flex:1"></select>
                        <input id="presset-qty" type="number" value="1" min="1" style="width:90px" />
                        <button class="btn-primary" id="presset-add">ADICIONAR</button>
                        <button class="btn-danger" id="presset-remove">RETIRAR</button>
                    </div>
                </div>`;
                vespertino.forEach(a => html += `<li><strong>ID:</strong> ${a.id} | <strong>Linha:</strong> ${a.linha}</li>`);
                html += `</ul></div>`;
                html += `<div class="armario-turno noturno"><h3>Noturno</h3><ul class="armario-lista">
                <div class="muted" style="display:flex; gap:8px; justify-content:flex-end; align-items:center;">Ajustar peça (adicionar/retirar)</div>
                <div style="display:flex; gap:8px; justify-content:flex-end; align-items:center;">
                    <div style="display:flex;gap:8px">
                        <select id="presset-select" style="flex:1"></select>
                        <input id="presset-qty" type="number" value="1" min="1" style="width:90px" />
                        <button class="btn-primary" id="presset-add">ADICIONAR</button>
                        <button class="btn-danger" id="presset-remove">RETIRAR</button>
                    </div>
                </div>`;
                noturno.forEach(a => html += `<li><strong>ID:</strong> ${a.id} | <strong>Linha:</strong> ${a.linha}</li>`);
                html += `</ul></div>`;

                document.getElementById('conteudo').innerHTML = html;
            })
            .catch(() => {
                document.getElementById('conteudo').innerHTML = '<p>Erro ao carregar armários.</p>';
            });
    }

    // Substitua os eventos dos botões do dropdown:
    document.querySelector('[data-page="armarios/armario1.php"]').addEventListener('click', function(e) {
        e.preventDefault();
        carregarArmariosPorLinhaSeparadoPorTurno('1');
    });
    document.querySelector('[data-page="armarios/armario2.php"]').addEventListener('click', function(e) {
        e.preventDefault();
        carregarArmariosPorLinhaSeparadoPorTurno('2');
    });
    document.querySelector('[data-page="armarios/armario3.php"]').addEventListener('click', function(e) {
        e.preventDefault();
        carregarArmariosPorLinhaSeparadoPorTurno('3');
    });
    document.querySelector('[data-page="armarios/armario4.php"]').addEventListener('click', function(e) {
        e.preventDefault();
        carregarArmariosPorLinhaSeparadoPorTurno('4');
    });
    document.querySelector('[data-page="armarios/armario5.php"]').addEventListener('click', function(e) {
        e.preventDefault();
        carregarArmariosPorLinhaSeparadoPorTurno('5');
    });
    document.querySelector('[data-page="armarios/armario6.php"]').addEventListener('click', function(e) {
        e.preventDefault();
        carregarArmariosPorLinhaSeparadoPorTurno('6');
    });
</script>

</html>