<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório</title>
    <link rel="stylesheet" href="./css/relatorio.css">
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

    <script src="./scripts/relatorio.js"></script>
</body>
</html>
