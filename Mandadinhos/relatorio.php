<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório</title>
    <style>
        textarea { width: 100%; height: 200px; font-size:14px; padding:8px; box-sizing:border-box }
        .result { white-space: pre-wrap; background:#f7f7f7; padding:10px; border:1px solid #ddd }
        /* limitar a altura do box e permitir scroll somente nele */
        #relatorios {
            max-height: 22%; /* ajuste conforme necessário */
            overflow: auto;
        }
        /* estilo interno dos blocos */
        #relatorios > div {
            background: #fff;
            border-radius: 6px;
        }
        @media (max-width: 600px) {
            #relatorios { max-height: 240px }
        }
        .btn-save {
            background: #ff0000ff;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 3px 6px rgba(0,0,0,0.12);
            transition: transform .08s ease, box-shadow .08s ease, opacity .08s ease;
            font-weight:600;
        }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,0,0,0.12) }
        .btn-save:active { transform: translateY(0); opacity: .95 }
        .btn-delete {
            background: #ff4d4f;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
        }
        .btn-delete:hover { opacity: .9 }
    </style>
</head>
<body>
    <h1>Relatório</h1>
    <form id="relForm">
        <textarea id="texto" name="texto" placeholder="Escreva o relatório..."></textarea>
        <div style="margin-top:8px">
            <button type="submit" class="btn-save">Salvar Relatório</button>
        </div>
    </form>

    <h2>Relatórios salvos</h2>
    <div id="relatorios" class="result">(carregando...)</div>

    <script>
        const apiBase = 'http://localhost:8000'; // ajuste se necessário

        async function carregar() {
            try {
                const res = await fetch(apiBase + '/relatorio');
                const data = await res.json();
                const list = data.relatorios || [];
                const container = document.getElementById('relatorios');
                container.innerHTML = '';
                if (list.length === 0) {
                    container.textContent = '(nenhum relatório)';
                    return;
                }
                list.reverse(); // mostrar o mais recente primeiro
                for (const item of list) {
                    const div = document.createElement('div');
                    div.style.marginBottom = '12px';
                    div.style.padding = '8px';
                    div.style.border = '1px solid #ccc';
                    const ts = item.timestamp ? `<div style="font-size:12px;color:#666">${item.timestamp}</div>` : '';
                    let deleteBtn = '';
                    if (item.id) {
                        deleteBtn = `<button class="btn-delete" data-id="${item.id}">Excluir</button>`;
                    } else {
                        deleteBtn = `<span style="font-size:12px;color:#999">(sem id - antigo)</span>`;
                    }
                    div.innerHTML = `${ts}<pre style="white-space:pre-wrap">${escapeHtml(item.texto || '')}</pre><div style="margin-top:6px">${deleteBtn}</div>`;
                    container.appendChild(div);
                }
                // attach delete handlers
                const dels = container.querySelectorAll('.btn-delete');
                dels.forEach(b => b.addEventListener('click', async (e) => {
                    const id = b.getAttribute('data-id');
                    if (!confirm('Confirma exclusão deste relatório?')) return;
                    try {
                        const res = await fetch(apiBase + '/relatorio/' + encodeURIComponent(id), { method: 'DELETE' });
                        const j = await res.json();
                        if (j.status === 'deleted') {
                            carregar();
                        } else {
                            alert('Não foi possível excluir (não encontrado)');
                        }
                    } catch (err) {
                        alert('Erro ao conectar com a API');
                    }
                }));
            } catch (e) {
                document.getElementById('relatorios').textContent = '(erro ao carregar)';
            }
        }

        document.getElementById('relForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const texto = document.getElementById('texto').value;
            try {
                const res = await fetch(apiBase + '/relatorio', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ texto })
                });
                const data = await res.json();
                if (data.status === 'ok') {
                    // adicionar o item retornado no topo
                    document.getElementById('texto').value = '';
                    carregar();
                } else {
                    alert('Erro ao salvar');
                }
            } catch (err) {
                alert('Erro de conexão com a API');
            }
        });

        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // carregar ao abrir
        carregar();
    </script>
</body>
</html>