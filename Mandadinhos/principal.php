<?php
// session_start();
// if (!isset($_SESSION['user'])) {
//     header("Location: index.php");
//     exit();
// }
// ?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./img/FAVICON.png" type="image/png">
    <link rel="stylesheet" href="./css/pgPrincipal.css">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <title>Página Principal</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #b90000ff;
            color: white;
        }

        .armario-turno {
            margin-bottom: 24px;
        }

        .armario-turno h3 {
            margin-bottom: 8px;
        }
    </style>
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
        $('#btnEx').click(() => $('#armarioDropdown').slideToggle(200));
        $('.dropdown-item').click(function() {
            const page = $(this).data('page');
            const match = page.match(/armario(\d+)\.php/);
            const linha = match ? match[1] : null;
            $('#conteudo').load(page, (response, status) => {
                if (status === "error") {
                    $('#conteudo').html('<p>Erro ao carregar conteúdo</p>');
                    return;
                }
                if (linha) carregarArmariosPorLinhaSeparadoPorTurno(linha);
            });
        });
        $('#btn2').click(() => $('#conteudo').load('relatorio.php'));
        $('#btn4').click(() => $('#conteudo').load('cadastros.php'));
        $('#btn3').click(() => {
            $('#conteudo').load('presset.php', function(response, status) {
                if (status === "error") {
                    $('#conteudo').html('<p>Erro ao carregar Presset</p>');
                    return;
                }
                // Chama a função que carrega os dados e preenche a tabela
                if (typeof carregar === "function") {
                    carregar();
                }
            });
        });

    });

    // -----------------------------
    // Funções armários
    // -----------------------------
    async function carregarArmariosPorLinhaSeparadoPorTurno(linha) {
        try {
            const armarios = await (await fetch(`http://localhost:8080/armarios/linha/${linha}`)).json();
            const ferramentas = await (await fetch('http://localhost:8080/ferramentas')).json();

            const turnosExibicao = {
                manha: "Manhã",
                tarde: "Tarde",
                noite: "Noite"
            };

            function removerAcentos(str) {
                return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
            }

            const turnos = {
                manha: armarios.filter(a => a.turno && removerAcentos(a.turno) === "manha"),
                tarde: armarios.filter(a => a.turno && removerAcentos(a.turno) === "tarde"),
                noite: armarios.filter(a => a.turno && removerAcentos(a.turno) === "noite")
            };

            let html = `<div class="armarios-titulo">Armários da Linha ${linha}</div>`;

            for (const [chave, dados] of Object.entries(turnos)) {
                html += `<div class="armario-turno">
                        <h3>${turnosExibicao[chave]}</h3>
                        <div style="display:flex; gap:8px; margin-bottom:8px;">
                            <select class="presset-select" style="flex:1"></select>
                            <input class="presset-qty" type="number" value="1" min="1" style="width:90px" />
                            <button class="btn-primary presset-add">ADICIONAR</button>
                            <button class="btn-danger presset-remove">RETIRAR</button>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ferramenta</th>
                                    <th>Qtd</th>
                                </tr>
                            </thead>
                            <tbody>`;

                dados.forEach(a => {
                    if (a.ferramentas && a.ferramentas.length > 0) {
                        a.ferramentas.forEach(f => {
                            html += `<tr>
                                    <td>${a.id}</td>
                                    <td>${f.nome}</td>
                                    <td>${f.qtd_estoque}</td>
                                </tr>`;
                        });
                    } else {
                        html += `<tr>
                                <td>${a.id}</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>`;
                    }
                });

                html += `</tbody></table></div>`;
            }

            document.getElementById('conteudo').innerHTML = html;

            // Preencher selects com ferramentas
            document.querySelectorAll('.presset-select').forEach(select => {
                ferramentas.forEach(f => {
                    const option = document.createElement('option');
                    option.value = f.id;
                    option.textContent = `${f.nome} (Qtd: ${f.qtd_estoque})`;
                    select.appendChild(option);
                });
            });

            adicionarEventosTransferencia(linha);

        } catch (error) {
            console.error(error);
            document.getElementById('conteudo').innerHTML = '<p>Erro ao carregar armários.</p>';
        }
    }

    function adicionarEventosTransferencia(linha) {
        document.querySelectorAll('.presset-add').forEach(btn => {
            btn.onclick = async e => {
                const container = e.target.closest('div');
                const turno = e.target.closest('.armario-turno').querySelector('h3').textContent;
                const select = container.querySelector('.presset-select');
                const qtyInput = container.querySelector('.presset-qty');
                const id = parseInt(select.value);
                const qtd = parseInt(qtyInput.value);
                const nome = select.options[select.selectedIndex].text.split(' (Qtd')[0];
                if (!id || qtd <= 0) return alert('Selecione uma ferramenta e quantidade válida.');
                await transferirParaArmario(id, nome, linha, turno, qtd);
                await atualizarTudo(linha);
            };
        });

        document.querySelectorAll('.presset-remove').forEach(btn => {
            btn.onclick = async e => {
                const container = e.target.closest('div');
                const turno = e.target.closest('.armario-turno').querySelector('h3').textContent;
                const select = container.querySelector('.presset-select');
                const qtyInput = container.querySelector('.presset-qty');
                const id = parseInt(select.value);
                const qtd = parseInt(qtyInput.value);
                const nome = select.options[select.selectedIndex].text.split(' (Qtd')[0];
                if (!id || qtd <= 0) return alert('Selecione uma ferramenta e quantidade válida.');
                await transferirParaArmario(id, nome, linha, turno, -qtd);
                await atualizarTudo(linha);
            };
        });
    }

    function formatarTurno(turno) {
        return turno.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    async function atualizarTudo(linha) {
        const ferramentas = await (await fetch('http://localhost:8080/ferramentas')).json();
        document.querySelectorAll('.presset-select').forEach(select => {
            select.innerHTML = '';
            ferramentas.forEach(f => {
                const option = document.createElement('option');
                option.value = f.id;
                option.textContent = `${f.nome} (Qtd: ${f.qtd_estoque})`;
                select.appendChild(option);
            });
        });
        await carregarArmariosPorLinhaSeparadoPorTurno(linha);
    }

    async function transferirParaArmario(id, nome, linha, turno, qtd) {
        const dados = {
            id_ferramenta: id,
            nome: nome,
            linha: linha,
            turno: turno,
            qtd_transferida: qtd
        };
        try {
            const res = await fetch('http://localhost:8080/transferir_presset_para_armario', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dados)
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.detail || 'Erro na transferência');
            alert(data.mensagem || 'Transferência realizada com sucesso!');
        } catch (err) {
            console.error(err);
            alert('Falha na transferência!\n' + err.message);
        }
    }
</script>

</html>