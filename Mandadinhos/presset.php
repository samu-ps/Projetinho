<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presset</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        div[style*="display:flex"] {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin: 20px 0;
        }

        div[style*="flex:1"] {
            flex: 1;
            background-color: #fff;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .muted {
            font-size: 1em;
            font-weight: bold;
            color: #555;
            margin-bottom: 12px;
        }

        #presset-table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }

        #presset-table th {
            background-color: #b90000ff;
            color: white;
            padding: 10px;
            text-align: left;
        }

        #presset-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        #presset-table tr:hover {
            background-color: #f1f1f1;
        }

        @media (max-width: 600px) {
            #presset-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>

<body>
    <div id="view-presset" class="card">
        <h3>PRESSET — Estoque Geral</h3>
        <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px;max-width:87%;">
            <div style="flex:1">
                <div class="muted">Pesquisar peça</div>
                <input id="presset-search" placeholder="Digite nome da peça..." style="width:100%" />
            </div>
            <div style="width:220px">
                <div class="muted">Ajustar peça (adicionar/retirar)</div>
                <div style="display:flex;gap:8px">
                    <select id="presset-select" style="flex:1"></select>
                    <input id="presset-qty" type="number" value="1" min="1" style="width:90px" />
                    <button class="btn-primary" id="presset-add">ADICIONAR</button>
                    <button class="btn-danger" id="presset-remove">RETIRAR</button>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:16px">
            <div style="flex:1">
                <div class="muted">Lista de peças no Presset</div>
                <table id="presset-table">
                    <thead>
                        <tr>
                            <th>Peça</th>
                            <th>Descrição</th>
                            <th>Un.</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <aside style="width:380px">
                <div class="muted">Movimentações recentes</div>
                <div id="log" class="log"></div>
            </aside>
        </div>
    </div>

    <script>
        window.dadosPresset = window.dadosPresset || [];

        async function carregar() {
            // tenta pegar do cache
            const cache = sessionStorage.getItem("presset_cache");
            if (cache) {
                window.dadosPresset = JSON.parse(cache);
                preencherTabela(dadosPresset);
                preencherSelect(dadosPresset);
            }

            // atualiza com o backend
            const res = await fetch('http://localhost:8080/ferramentas');
            if (res.ok) {
                const novosDados = await res.json();
                if (novosDados.length > 0 || !cache) {
                    window.dadosPresset = novosDados;
                    preencherTabela(dadosPresset);
                    preencherSelect(dadosPresset);
                    sessionStorage.setItem("presset_cache", JSON.stringify(dadosPresset));
                }
            } else {
                console.error("Erro no fetch:", res.status);
            }
        }

        function preencherTabela(dados) {
            const tbody = document.querySelector("#presset-table tbody");
            tbody.innerHTML = "";
            dados.forEach(c => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${c.nome}</td>
                    <td>${c.descricao}</td>
                    <td>${c.qtd_estoque}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        function preencherSelect(dados) {
            const select = document.getElementById("presset-select");
            select.innerHTML = "";
            dados.forEach(c => {
                const option = document.createElement("option");
                option.value = c.id;
                option.textContent = c.nome;
                select.appendChild(option);
            });
        }

        async function atualizarQuantidade(id, delta) {
            const item = dadosPresset.find(c => c.id == id);
            if (!item) return;

            const novaQtd = Math.max(0, item.qtd_estoque + delta);
            item.qtd_estoque = novaQtd;
            preencherTabela(dadosPresset);

            // Atualiza backend
            const res = await fetch(`http://localhost:8080/ferramentas/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    qtd_estoque: novaQtd
                })
            });

            if (res.ok) {
                sessionStorage.setItem("presset_cache", JSON.stringify(dadosPresset));
                registrarLog(item.nome, delta);
            } else {
                alert("Erro ao atualizar no servidor.");
            }
        }

        function registrarLog(nome, delta) {
            const log = document.getElementById("log");
            const op = delta > 0 ? "Adicionado" : "Retirado";
            const div = document.createElement("div");
            div.textContent = `${op} ${Math.abs(delta)} de ${nome}`;
            log.prepend(div);
        }

        // --- Delegação de eventos (não duplica listeners) ---
        document.getElementById("view-presset").addEventListener("click", e => {
            if (e.target.id === "presset-add") {
                const id = document.getElementById("presset-select").value;
                const qtd = parseInt(document.getElementById("presset-qty").value);
                atualizarQuantidade(id, qtd);
            } else if (e.target.id === "presset-remove") {
                const id = document.getElementById("presset-select").value;
                const qtd = parseInt(document.getElementById("presset-qty").value);
                atualizarQuantidade(id, -qtd);
            }
        });

        // Inicializa
        carregar();
    </script>
</body>

</html>