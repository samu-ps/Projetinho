<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="shortcut icon" href="./img/logo copy.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/cadastros.css">
</head>

<body>
    <div class="form-center">
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
        </div>
    </div>

</body>

</html>