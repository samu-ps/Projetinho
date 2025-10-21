<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="shortcut icon" href="./img/logo copy.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <div class="form-card">
        <form id="formFerramentas">
            <h1>Ferramentas</h1>
            <div class="input-group">
                <input type="text" class="form-control" name="nome" placeholder="Nome da Ferramenta" required>
                <input type="text" class="form-control" name="descricao" placeholder="Descrição" required>
                <input type="text" class="form-control" name="vida_util" placeholder="Vida útil da ferramenta">
                <input type="number" class="form-control" name="qtd_estoque" placeholder="Quantidade no estoque">
            </div>
            <div class="botoes">
                <button type="submit" class="btn btn-danger">Enviar</button>
            </div>
        </form>
        <div id="msgFerramentas"></div>
    </div>

    <div id="listaArmarios"></div>

    <script>
        // =================== FERRAMENTAS ===================
        document.getElementById('formFerramentas').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;

            const dados = {
                nome: form.nome.value,
                descricao: form.descricao.value,
                vida_util: form.vida_util.value || null,
                qtd_estoque: form.qtd_estoque.value || 0
            };
            try {
                const response = await fetch('http://localhost:8080/ferramentas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dados)
                });

                const data = await response.json();
                document.getElementById('msgFerramentas').innerHTML = '<p style="color:green;">' + data.status + '</p>';
                form.reset();

                // 🔹 NOVO: Atualiza automaticamente o Presset (estoque geral)
                // Envia a nova ferramenta para a tabela do Presset via FastAPI ou evento local
                await fetch('http://localhost:8080/presset/sincronizar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        nome: dados.nome,
                        qtd: dados.qtd_estoque,
                        unidade: 'un'
                    })
                }).catch(err => console.warn('Presset não sincronizado (endpoint opcional):', err));

                // 🔹 Opcional: emite evento local para atualizar tabela se o Presset estiver aberto
                document.dispatchEvent(new CustomEvent('atualizarPresset'));

            } catch (err) {
                console.error(err);
                document.getElementById('msgFerramentas').innerHTML = '<p style="color:red;">Erro ao cadastrar ferramenta.</p>';
            }
        });
    </script>
</body>

</html>