$(document).ready(function () {

    // --- CARREGAR PRESSET AO INICIAR ---
    $('#conteudo').load('presset.php', function (response, status) {
        if (status === "error") {
            $('#conteudo').html('<p>Erro ao carregar Presset</p>');
            return;
        }
        if (typeof carregar === "function") {
            carregar();
        }
    });

    // Eventos de clique dos botões
    $('#btnEx').click(() => $('#armarioDropdown').slideToggle(200));
    $('.dropdown-item').click(function () {
        const page = $(this).data('page');
        const match = page.match(/armario(\d+)\.php/);
        const linha = match ? match[1] : null;
        $('#conteudo').load(page, (response, status) => {
            if (status === "error") {
                $('#conteudo').html('<p>Erro ao carregar conteúdo</p>');
                return;
            }
            if (linha) carregarArmariosPorLinhaSeparadoPorTurno(linha);
        });
    });
    $('#btn2').click(() => $('#conteudo').load('relatorio.php'));
    $('#btn4').click(() => $('#conteudo').load('cadastros.php'));
    $('#btn10').click(() => $('#conteudo').load('sobre.php'));
    $('#btn3').click(() => {
        $('#conteudo').load('presset.php', function (response, status) {
            if (status === "error") {
                $('#conteudo').html('<p>Erro ao carregar Presset</p>');
                return;
            }
            if (typeof carregar === "function") {
                carregar();
            }
        });
    });

});

// -----------------------------
// Funções armários
// -----------------------------
async function carregarArmariosPorLinhaSeparadoPorTurno(linha) {
    try {
        const armarios = await (await fetch(`http://localhost:8080/armarios/linha/${linha}`)).json();
        const ferramentas = await (await fetch('http://localhost:8080/ferramentas')).json();

        const turnosExibicao = {
            manha: "Manhã",
            tarde: "Tarde",
            noite: "Noite"
        };

        function removerAcentos(str) {
            return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
        }

        const turnos = {
            manha: armarios.filter(a => a.turno && removerAcentos(a.turno) === "manha"),
            tarde: armarios.filter(a => a.turno && removerAcentos(a.turno) === "tarde"),
            noite: armarios.filter(a => a.turno && removerAcentos(a.turno) === "noite")
        };

        let html = `<div class="armarios-titulo">Armários da Linha ${linha}</div>`;

        for (const [chave, dados] of Object.entries(turnos)) {
            html += `<div class="armario-turno">
                    <h3>${turnosExibicao[chave]}</h3>
                    <div style="display:flex; gap:8px; margin-bottom:8px;">
                        <select class="presset-select"></select>
                        <input class="presset-qty" type="number" value="1" min="1" style="width:90px" />
                        <button class="btn-primary presset-add">ADICIONAR</button>
                        <button class="btn-danger presset-remove">RETIRAR</button>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ferramenta</th>
                                <th>Qtd</th>
                            </tr>
                        </thead>
                        <tbody>`;

            dados.forEach(a => {
                if (a.ferramentas && a.ferramentas.length > 0) {
                    a.ferramentas.forEach(f => {
                        html += `<tr data-ferramenta="${f.id}">
                                <td>${a.id}</td>
                                <td>${f.nome}</td>
                                <td class="qtd-armario">${f.qtd_estoque}</td>
                            </tr>`;
                    });
                } else {
                    html += `<tr>
                            <td>${a.id}</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>`;
                }
            });

            html += `</tbody></table></div>`;
        }

        document.getElementById('conteudo').innerHTML = html;

        // Preencher selects com ferramentas
        document.querySelectorAll('.presset-select').forEach(select => {
            ferramentas.forEach(f => {
                const option = document.createElement('option');
                option.value = f.id;
                option.textContent = `${f.nome} (Qtd: ${f.qtd_estoque})`;
                select.appendChild(option);
            });
        });

        adicionarEventosTransferencia(linha);

    } catch (error) {
        console.error(error);
        document.getElementById('conteudo').innerHTML = '<p>Erro ao carregar armários.</p>';
    }
}

// -----------------------------
// Modais
// -----------------------------
function mostrarErroModal(mensagem) {
    const modal = document.getElementById('modalErro');
    const msg = document.getElementById('msgErro');
    msg.textContent = mensagem;
    modal.style.display = 'flex';
    document.getElementById('btnOkErro').onclick = () => {
        modal.style.display = 'none';
    };
}

// ✅ Modal de sucesso/info
function mostrarMensagem(titulo, texto) {
    const modal = document.getElementById("msgModal");
    const tituloElem = document.getElementById("msgModalTitulo");
    const textoElem = document.getElementById("msgModalTexto");
    const btnFechar = document.getElementById("msgModalFechar");

    tituloElem.textContent = titulo;
    textoElem.textContent = texto;
    modal.style.display = "flex";

    btnFechar.onclick = () => modal.style.display = "none";
    modal.onclick = (e) => { if (e.target === modal) modal.style.display = "none"; };
}

// -----------------------------
// Eventos de transferência
// -----------------------------
function adicionarEventosTransferencia(linha) {
    // --- ADICIONAR ---
    document.querySelectorAll('.presset-add').forEach(btn => {
        btn.onclick = async e => {
            const container = e.target.closest('div');
            const turno = e.target.closest('.armario-turno').querySelector('h3').textContent;
            const select = container.querySelector('.presset-select');
            const qtyInput = container.querySelector('.presset-qty');
            const id = parseInt(select.value);
            const qtd = parseInt(qtyInput.value);
            const nome = select.options[select.selectedIndex].text.split(' (Qtd')[0];

            if (!id || qtd <= 0) {
                mostrarErroModal('Selecione uma ferramenta e quantidade válida.');
                return;
            }

            const qtdDisponivel = parseInt(select.options[select.selectedIndex].text.match(/Qtd:\s*(\d+)/)[1]);
            if (qtd > qtdDisponivel) {
                mostrarErroModal(`Quantidade insuficiente no Presset!\nDisponível: ${qtdDisponivel}, solicitado: ${qtd}.`);
                return;
            }

            await transferirParaArmario(id, nome, linha, turno, qtd);
            await atualizarTudo(linha);
        };
    });

    // --- RETIRAR ---
    document.querySelectorAll('.presset-remove').forEach(btn => {
        btn.onclick = async e => {
            const container = e.target.closest('div');
            const turno = e.target.closest('.armario-turno').querySelector('h3').textContent;
            const select = container.querySelector('.presset-select');
            const qtyInput = container.querySelector('.presset-qty');
            const id = parseInt(select.value);
            const qtd = parseInt(qtyInput.value);
            const nome = select.options[select.selectedIndex].text.split(' (Qtd')[0];

            if (!id || qtd <= 0) {
                mostrarErroModal('Selecione uma ferramenta e quantidade válida.');
                return;
            }

            const linhaTurno = e.target.closest('.armario-turno');
            const linhaFerramenta = linhaTurno.querySelector(`tr[data-ferramenta="${id}"]`);
            if (!linhaFerramenta) {
                mostrarErroModal(`A ferramenta "${nome}" não está presente neste armário.`);
                return;
            }

            const qtdArmario = parseInt(linhaFerramenta.querySelector('.qtd-armario').textContent);
            if (qtd > qtdArmario) {
                mostrarErroModal(`Quantidade insuficiente no armário!\nDisponível: ${qtdArmario}, solicitado: ${qtd}.`);
                return;
            }

            await transferirParaArmario(id, nome, linha, turno, -qtd);
            await atualizarTudo(linha);
        };
    });
}

// -----------------------------
// Atualizar e enviar dados
// -----------------------------
async function atualizarTudo(linha) {
    const ferramentas = await (await fetch('http://localhost:8080/ferramentas')).json();
    document.querySelectorAll('.presset-select').forEach(select => {
        select.innerHTML = '';
        ferramentas.forEach(f => {
            const option = document.createElement('option');
            option.value = f.id;
            option.textContent = `${f.nome} (Qtd: ${f.qtd_estoque})`;
            select.appendChild(option);
        });
    });
    await carregarArmariosPorLinhaSeparadoPorTurno(linha);
}

async function transferirParaArmario(id, nome, linha, turno, qtd) {
    const dados = {
        id_ferramenta: id,
        nome: nome,
        linha: linha,
        turno: turno,
        qtd_transferida: qtd
    };
    try {
        const res = await fetch('http://localhost:8080/transferir_presset_para_armario', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dados)
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.detail || 'Erro na transferência');

        // ✅ Usa modal de sucesso
        mostrarMensagem("Sucesso", data.mensagem || 'Transferência realizada com sucesso!');
    } catch (err) {
        console.error(err);
        mostrarErroModal('Falha na transferência!\n' + err.message);
    }
}
setTimeout(() => {
    modal.style.display = "none";
}, 3000);