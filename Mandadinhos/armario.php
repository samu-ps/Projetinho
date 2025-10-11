<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <!-- <link rel="stylesheet" href="./estilo/style.css"> -->
    <link rel="shortcut icon" href="./img/logo copy.png" type="image/x-icon">
    '<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"> -->
    <!-- <link rel="stylesheet" href="./css/style.css"> -->
</head>
<body>
    <div class="form-card">
        <form id="formArmario">
            <h1>Armário</h1>
            <div class="input-group">
                <input type="number" class="form-control" name="id" id="idArmario" placeholder="ID" >
                <input type="text" class="form-control" name="turno" placeholder="Turno" required>
                <input type="text" class="form-control" name="linha" placeholder="Linha" required>
                <input type="number" class="form-control" name="funcionario_id" placeholder="ID Funcionário">
                <input type="number" class="form-control" name="qtd_prevista" placeholder="Quantidade Prevista">
            </div>
            <div class="botoes">
                <button type="submit" class="btn btn-danger" id="entrar">Enviar</button>
            </div>
        </form>
        <div id="msgArmario"></div>
    </div>
    <div id="listaArmarios"></div>
</body>
    <script src="JOAO-main/assets/scripr.js"></script>
    <script>
function carregarArmarios() {
    fetch('http://localhost:8000/armarios')
        .then(response => response.json())
        .then(data => {
            let html = '<h2>Armários cadastrados</h2><ul>';
            data.forEach(armario => {
                html += `<li>
                    ID: ${armario.id} | Turno: ${armario.turno} | Linha: ${armario.linha}
                    <button onclick="deletarArmario(${armario.id})" style="margin-left:10px;color:#fff;background:#d9534f;border:none;padding:4px 10px;border-radius:4px;cursor:pointer;">Deletar</button>
                </li>`;
            });
            html += '</ul>';
            document.getElementById('listaArmarios').innerHTML = html;
        });
}

function deletarArmario(id) {
    if (!confirm('Tem certeza que deseja deletar este armário?')) return;
    fetch(`http://localhost:8000/armarios/${id}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('msgArmario').innerHTML = '<p style="color:green;">' + data.status + '</p>';
        carregarArmarios();
    })
    .catch(() => {
        document.getElementById('msgArmario').innerHTML = '<p style="color:red;">Erro ao deletar armário.</p>';
    });
}

// Carregar lista ao abrir a página e após cadastro
document.addEventListener('DOMContentLoaded', function() {
    fetch('http://localhost:8000/armarios/ultimo_id')
        .then(response => response.json())
        .then(data => {
            const idInput = document.querySelector('input[name="id"]');
            idInput.value = data.proximo_id;
            idInput.readOnly = true;
        });
    carregarArmarios();
});

document.getElementById('formArmario').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const dados = {
        turno: form.turno.value,
        linha: form.linha.value,
        funcionario_id: form.funcionario_id.value || null,
        qtd_prevista: form.qtd_prevista.value || null
    };
    fetch('http://localhost:8000/armarios', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(dados)
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('msgArmario').innerHTML = '<p style="color:green;">' + data.status + '</p>';
        form.reset();
        // Atualiza o próximo ID após cadastro
        fetch('http://localhost:8000/armarios/ultimo_id')
            .then(response => response.json())
            .then(data => {
                form.id.value = data.proximo_id;
                form.id.readOnly = true;
            });
        carregarArmarios();
    })
    .catch(() => {
        document.getElementById('msgArmario').innerHTML = '<p style="color:red;">Erro ao cadastrar armário.</p>';
    });
});
    </script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script> -->
</html>
