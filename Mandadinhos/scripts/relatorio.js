        const API_URL = "http://localhost:8080"; // ajuste se usar outra porta

        async function carregarRelatorios() {
            const res = await fetch(`${API_URL}/relatorios`);
            const data = await res.json();
            const box = document.getElementById('relatorios');
            box.innerHTML = '';
            data.forEach((item, i) => {
                const div = document.createElement('div');
                div.textContent = `[${item.data}] ${item.texto}`;
                box.appendChild(div);
            });
        }

        document.getElementById('relForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const texto = document.getElementById('texto').value.trim();
            if (!texto) return alert("Escreva algo antes de salvar.");
            await fetch(`${API_URL}/salvar_relatorio`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ texto })
            });
            document.getElementById('texto').value = '';
            carregarRelatorios();
        });

        carregarRelatorios();