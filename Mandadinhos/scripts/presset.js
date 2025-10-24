window.dadosPresset = window.dadosPresset || [];

// -----------------------------
// Carregar dados do Presset
// -----------------------------
async function carregar() {
    const cache = sessionStorage.getItem("presset_cache");
    if (cache) {
        window.dadosPresset = JSON.parse(cache);
        preencherTabela(dadosPresset);
    }

    const res = await fetch('http://localhost:8080/ferramentas');
    if (res.ok) {
        const novosDados = await res.json();
        if (novosDados.length > 0 || !cache) {
            window.dadosPresset = novosDados;
            preencherTabela(dadosPresset);
            sessionStorage.setItem("presset_cache", JSON.stringify(dadosPresset));
        }
    } else {
        console.error("Erro no fetch:", res.status);
    }
}

// -----------------------------
// Preencher tabela
// -----------------------------
function preencherTabela(dados) {
    const tbody = document.querySelector("#presset-table tbody");
    tbody.innerHTML = "";

    dados.forEach(c => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${c.nome}</td>
            <td>${c.descricao}</td>
            <td>${c.qtd_estoque}</td>
            <td></td>
        `;
        tbody.appendChild(tr);
    });

    // Reaplicar eventos de exclusão
    adicionarEventosExcluir();
}

// -----------------------------
// Atualizar select
// -----------------------------
function preencherSelect(dados) {
    const select = document.getElementById("presset-select");
    select.innerHTML = "";
    dados.forEach(c => {
        const option = document.createElement("option");
        option.value = c.id;
        option.textContent = c.nome;
        select.appendChild(option);
    });
}

// -----------------------------
// Atualizar quantidade
// -----------------------------
async function atualizarQuantidade(id, delta) {
    const item = dadosPresset.find(c => c.id == id);
    if (!item) return;

    const novaQtd = Math.max(0, item.qtd_estoque + delta);
    item.qtd_estoque = novaQtd;
    preencherTabela(dadosPresset);

    // Atualiza backend
    const res = await fetch(`http://localhost:8080/ferramentas/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ qtd_estoque: novaQtd })
    });

    if (res.ok) {
        sessionStorage.setItem("presset_cache", JSON.stringify(dadosPresset));
        mostrarModal(`${delta > 0 ? "Adicionado" : "Retirado"} ${Math.abs(delta)} de ${item.nome}`);
    } else {
        mostrarModal("Erro ao atualizar no servidor.");
    }
}

// -----------------------------
// Modal de mensagem
// -----------------------------
function mostrarModal(mensagem) {
    const modal = document.getElementById("msgModal");
    const content = document.getElementById("msgModalContent");
    content.textContent = mensagem;
    modal.style.display = "flex";

    setTimeout(() => modal.style.display = "none", 2000);
}

// -----------------------------
// Eventos de exclusão
// -----------------------------
function adicionarEventosExcluir() {
    document.querySelectorAll(".btn-excluir").forEach(btn => {
        btn.onclick = async () => {
            const id = btn.getAttribute("data-id");
            if (!confirm("Tem certeza que deseja excluir esta ferramenta?")) return;
            try {
                const res = await fetch(`http://localhost:8080/ferramentas/${id}`, { method: "DELETE" });
                if (!res.ok) throw new Error("Falha ao excluir ferramenta.");

                window.dadosPresset = window.dadosPresset.filter(f => f.id != id);
                preencherTabela(window.dadosPresset);
                mostrarModal("Ferramenta excluída com sucesso!");
            } catch (err) {
                console.error(err);
                mostrarModal("Erro ao excluir ferramenta.");
            }
        };
    });
}

// -----------------------------
// Delegação de eventos para adicionar/retirar
// -----------------------------
document.getElementById("view-presset").addEventListener("click", e => {
    const target = e.target;

    if (target.id === "presset-add") {
        const id = document.getElementById("presset-select").value;
        const qtd = parseInt(document.getElementById("presset-qty").value);
        atualizarQuantidade(id, qtd);

    } else if (target.id === "presset-remove") {
        const id = document.getElementById("presset-select").value;
        const qtd = parseInt(document.getElementById("presset-qty").value);
        atualizarQuantidade(id, -qtd);
    }
});

// -----------------------------
// Busca na tabela
// -----------------------------
document.getElementById("presset-search").addEventListener("input", e => {
    const termo = e.target.value.trim().toLowerCase();
    document.querySelectorAll("#presset-table tbody tr").forEach(tr => {
        const nome = tr.children[0].textContent.toLowerCase();
        const descricao = tr.children[1].textContent.toLowerCase();
        tr.style.display = (nome.includes(termo) || descricao.includes(termo)) ? "" : "none";
    });
});

// -----------------------------
// Inicialização
// -----------------------------
carregar();
preencherSelect(dadosPresset);
