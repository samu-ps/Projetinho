<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presset</title>
    <link rel="stylesheet" href="css/principal.css">
    <link rel="stylesheet" href="css/presset.css">

</head>

<body>
    <div id="view-presset" class="card">
        <h3>PRESSET — Estoque Geral</h3>
        <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px;max-width:79%;">
            <div style="flex:1; max-width:90%">
                <div class="muted">Pesquisar peça</div>
                <input id="presset-search" placeholder="Digite nome da peça..." style="width:96%" />
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
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <aside style="width:380px;display:flex;flex-direction:column;gap:20px;">
                <div>
                    <div class="muted">Ajustar peça (adicionar/retirar)</div>
                    <div class="ajustar-container">
                        <select id="presset-select"></select>
                        <input id="presset-qty" type="number" value="1" min="1" />
                        <button class="btn-primary" id="presset-add">ADICIONAR</button>
                        <button class="btn-danger" id="presset-remove">RETIRAR</button>
                    </div>
                </div>

                <div>
                    <div class="muted">Movimentações recentes</div>
                    <div id="log" class="log"></div>
                </div>
            </aside>
        </div>
    </div>
    <div id="msgModal" class="modal" style="display:none;">
        <div class="modal-content" id="msgModalContent"></div>
    </div>
    <script src="scripts/presset.js"></script>
</body>

</html>