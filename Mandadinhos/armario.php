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
    

    <div class="mb-3">
        <div class="form-card">
            <form action="processa.php" method="POST">
                <h1>Armário</h1>
                <div class="input-group">
                    <input type="text" name="id" class="form-control" placeholder="id" required>
                    <input type="text" class="form-control" name="id_ferramenta" placeholder="turno" required>
                    <input type="text" class="form-control" name="quantidade" placeholder="linha" required>
                    <input type="text" class="form-control" name="data" placeholder="id funcionário" required>
                    <input type="text" class="form-control" name="quantidade_minima" placeholder="quantidade prevista" required>
                </div>
                <div class="botoes">
                    <button type="submit" class="btn btn-danger" id="entrar">Enviar</button>
                </div>
            </form>
        </div>
    </div>
    
    
</body>
    <script src="JOAO-main/assets/scripr.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script> -->
</html>
