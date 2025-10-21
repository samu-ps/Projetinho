<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório</title>
    <style>
        textarea { width: 100%; height: 200px; font-size:14px; padding:8px; box-sizing:border-box }
        .result { white-space: pre-wrap; background:#f7f7f7; padding:10px; border:1px solid #ddd }
        #relatorios { max-height: 240px; overflow-y:auto }
        #relatorios > div { background:#fff; margin:6px 0; padding:8px; border-radius:6px }
        .btn-save {
            background: #ff0000ff; color: white; border: none;
            padding: 10px 16px; border-radius: 8px; cursor: pointer;
            box-shadow: 0 3px 6px rgba(0,0,0,0.12); transition: .1s; font-weight:600;
        }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,0,0,0.12) }
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
        const API_URL = "http://localhost:8080"; // ajuste se usar outra porta

        async function carregarRelatorios() {
            const res = await fetch(`${API_URL}/relatorios`);
            const data = await res.json();
            const box = document.getElementById('relatorios');
            box.innerHTML = '';
            data.forEach((item, i) => {
                const div = document.createElement('div');
                div.textContent = `[${item.data}] ${item.texto}`;
                box.appendChild(div);
            });
        }

        document.getElementById('relForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const texto = document.getElementById('texto').value.trim();
            if (!texto) return alert("Escreva algo antes de salvar.");
            await fetch(`${API_URL}/salvar_relatorio`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ texto })
            });
            document.getElementById('texto').value = '';
            carregarRelatorios();
        });

        carregarRelatorios();
    </script>
</body>
</html>
