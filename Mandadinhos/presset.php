<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presset</title>
    <link rel="stylesheet" href="./css/style.css">
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
                            <th>Qtd</th>
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
</body>

</html>