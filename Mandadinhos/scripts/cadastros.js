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


            } catch (err) {
                console.error(err);
                document.getElementById('msgFerramentas').innerHTML = '<p style="color:red;">Erro ao cadastrar ferramenta.</p>';
            }
        });