window.dadosPresset = window.dadosPresset || [];

async function carregar() {
    // tenta pegar do cache
    const cache = sessionStorage.getItem("presset_cache");
    if (cache) {
        window.dadosPresset = JSON.parse(cache);
        preencherTabela(dadosPresset);
        preencherSelect(dadosPresset);
    }

    // atualiza com o backend
    const res = await fetch('http://localhost:8080/ferramentas');
    if (res.ok) {
        const novosDados = await res.json();
        if (novosDados.length > 0 || !cache) {
            window.dadosPresset = novosDados;
            preencherTabela(dadosPresset);
            preencherSelect(dadosPresset);
            sessionStorage.setItem("presset_cache", JSON.stringify(dadosPresset));
        }
    } else {
        console.error("Erro no fetch:", res.status);
    }
}

function preencherTabela(dados) {
    const tbody = document.querySelector("#presset-table tbody");
    tbody.innerHTML = "";
    dados.forEach(c => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
                    <td>${c.nome}</td>
                    <td>${c.descricao}</td>
                    <td>${c.qtd_estoque}</td>
                    <td><button class="btn-delete" data-id="${c.id}">Excluir</button></td>
                `;
        tbody.appendChild(tr);
    });
}

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

async function atualizarQuantidade(id, delta) {
    const item = dadosPresset.find(c => c.id == id);
    if (!item) return;

    const novaQtd = Math.max(0, item.qtd_estoque + delta);
    item.qtd_estoque = novaQtd;
    preencherTabela(dadosPresset);

    // Atualiza backend
    const res = await fetch(`http://localhost:8080/ferramentas/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            qtd_estoque: novaQtd
        })
    });

    if (res.ok) {
        sessionStorage.setItem("presset_cache", JSON.stringify(dadosPresset));
        registrarLog(item.nome, delta);
    } else {
        alert("Erro ao atualizar no servidor.");
    }
}

function registrarLog(nome, delta) {
    const log = document.getElementById("log");
    const op = delta > 0 ? "Adicionado" : "Retirado";
    const div = document.createElement("div");
    div.textContent = `${op} ${Math.abs(delta)} de ${nome}`;
    log.prepend(div);
}

// --- Delegação de eventos (não duplica listeners) ---
document.getElementById("view-presset").addEventListener("click", async e => {
    const target = e.target;

    // Adicionar
    if (target.id === "presset-add") {
        const id = document.getElementById("presset-select").value;
        const qtd = parseInt(document.getElementById("presset-qty").value);
        atualizarQuantidade(id, qtd);

        // Retirar
    } else if (target.id === "presset-remove") {
        const id = document.getElementById("presset-select").value;
        const qtd = parseInt(document.getElementById("presset-qty").value);
        atualizarQuantidade(id, -qtd);

        // Excluir ferramenta
    } else if (target.classList.contains("btn-delete")) {
        const id = target.getAttribute("data-id");
        if (!confirm("Tem certeza que deseja excluir esta ferramenta?")) return;

        try {
            const res = await fetch(`http://localhost:8080/ferramentas/${id}`, {
                method: "DELETE"
            });
            if (!res.ok) throw new Error("Falha ao excluir ferramenta.");

            // Remove do array local e atualiza tabela
            window.dadosPresset = window.dadosPresset.filter(f => f.id != id);
            preencherTabela(window.dadosPresset);
        } catch (err) {
            console.error(err);
            alert("Erro ao excluir ferramenta.");
        }
    }
});

// Inicializa
carregar();

document.getElementById("presset-search").addEventListener("input", e => {
    const termo = e.target.value.trim().toLowerCase();
    const tbody = document.querySelector("#presset-table tbody");
    const linhas = tbody.querySelectorAll("tr");

    linhas.forEach(tr => {
        const nome = tr.children[0].textContent.toLowerCase();
        const descricao = tr.children[1].textContent.toLowerCase();

        // se o termo estiver contido em nome OU descrição → mostra
        if (nome.includes(termo)) {
            tr.style.display = "";
        } else {
            tr.style.display = "none";
        }
    });
});
// Adiciona evento de exclusão
document.querySelectorAll(".btn-delete").forEach(btn => {
    btn.onclick = async () => {
        const id = btn.getAttribute("data-id");
        if (!confirm("Tem certeza que deseja excluir esta ferramenta?")) return;
        try {
            const res = await fetch(`http://localhost:8080/ferramentas/${id}`, {
                method: "DELETE"
            });
            if (!res.ok) throw new Error("Falha ao excluir ferramenta.");
            // Remove do array local e atualiza tabela
            window.dadosPresset = window.dadosPresset.filter(f => f.id != id);
            preencherTabela(window.dadosPresset);
        } catch (err) {
            console.error(err);
            alert("Erro ao excluir ferramenta.");
        }
    };
});
