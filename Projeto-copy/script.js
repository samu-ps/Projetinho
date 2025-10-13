    /* ====== Dados Iniciais (fictícios) ====== */
        const initialState = {
        pieces: [
            {id: 'p1', name: 'Broca 3mm', unit: 'un', qty: 120},
            {id: 'p2', name: 'Pastilha A', unit: 'un', qty: 400},
            {id: 'p3', name: 'Parafuso M6', unit: 'pct', qty: 50},
            {id: 'p4', name: 'Porta-ferramenta', unit: 'un', qty: 8}
        ],
        armarios: {}, // gerado abaixo: 6 armarios, cada um com 3 gavetas
        logs: [],
        employees: [],
        machines: [],
        suppliers: []
        }

        function makeEmptyArmarios(){
        const arm = {}
        for(let i=1;i<=6;i++){
            arm['A'+i] = {
            id: 'A'+i,
            name: 'Armário '+i,
            drawers: {
                T1: [], // array of {pieceId, qty}
                T2: [],
                T3: []
            }
            }
        }
        return arm
        }

        // load or init
        const STORE_KEY = 'streparava_demo_v1'
        let state = JSON.parse(localStorage.getItem(STORE_KEY) || 'null')
        if(!state){
        initialState.armarios = makeEmptyArmarios()
        state = initialState
        save()
        }

        // helpers
        function save(){ localStorage.setItem(STORE_KEY, JSON.stringify(state)) }
        function uid(prefix='id'){return prefix+Math.random().toString(36).slice(2,9)}

        /* ====== UI REFERÊNCIAS ====== */
        const navPresset = document.getElementById('nav-presset')
        const navArmarios = document.getElementById('nav-armarios')
        const navCadastro = document.getElementById('nav-cadastro')
        const viewPresset = document.getElementById('view-presset')
        const viewArmarios = document.getElementById('view-armarios')
        const viewCadastro = document.getElementById('view-cadastro')

        const pressetTableBody = document.querySelector('#presset-table tbody')
        const pressetSelect = document.getElementById('presset-select')
        const pressetQty = document.getElementById('presset-qty')
        const pressetAdd = document.getElementById('presset-add')
        const pressetRemove = document.getElementById('presset-remove')
        const pressetSearch = document.getElementById('presset-search')

        const armarioTabs = document.getElementById('armario-tabs')
        const armarioTableBody = document.querySelector('#armario-table tbody')
        const armarioPieceSelect = document.getElementById('armario-piece')
        const armarioQty = document.getElementById('armario-qty')
        const toArmarioBtn = document.getElementById('to-armario')
        const fromArmarioBtn = document.getElementById('from-armario')
        const currentGaveta = document.getElementById('current-gaveta')
        const armarioSummary = document.getElementById('armario-summary')

        const logEl = document.getElementById('log')

        const addPieceName = document.getElementById('new-piece-name')
        const addPieceUnit = document.getElementById('new-piece-unit')
        const addPieceBtn = document.getElementById('add-piece')
        const listsEl = document.getElementById('lists')

        const countPresset = document.getElementById('count-presset')

        /* ====== Navegação simples ====== */
        navPresset.addEventListener('click',()=>{setView('presset')})
        navArmarios.addEventListener('click',()=>{setView('armarios')})
        navCadastro.addEventListener('click',()=>{setView('cadastro')})

        function setView(v){
        navPresset.classList.remove('active')
        navArmarios.classList.remove('active')
        navCadastro.classList.remove('active')
        viewPresset.style.display='none'
        viewArmarios.style.display='none'
        viewCadastro.style.display='none'
        if(v==='presset'){navPresset.classList.add('active');viewPresset.style.display='block'}
        if(v==='armarios'){navArmarios.classList.add('active');viewArmarios.style.display='block'}
        if(v==='cadastro'){navCadastro.classList.add('active');viewCadastro.style.display='block'}
        renderAll()
        }

        /* ====== Render ====== */
        function renderAll(){ renderPresset(); renderArmarioTabs(); renderCadastroLists(); renderLog(); }

        function renderPresset(){
        // table
        pressetTableBody.innerHTML=''
        const filter = pressetSearch.value.trim().toLowerCase()
        state.pieces.forEach(p=>{
            if(filter && !p.name.toLowerCase().includes(filter)) return
            const tr = document.createElement('tr')
            tr.innerHTML = `<td>${p.name}</td><td>${p.qty}</td><td>${p.unit}</td>`
            pressetTableBody.appendChild(tr)
        })

        // select
        pressetSelect.innerHTML = ''
        armarioPieceSelect.innerHTML = ''
        state.pieces.forEach(p=>{
            const op = document.createElement('option');op.value=p.id;op.textContent=p.name
            pressetSelect.appendChild(op)
            const op2 = op.cloneNode(true); armarioPieceSelect.appendChild(op2)
        })

        // counts
        const total = state.pieces.reduce((s,p)=>s+p.qty,0)
        countPresset.textContent = total
        }

        function renderArmarioTabs(){
        armarioTabs.innerHTML=''
        Object.values(state.armarios).forEach(ar=>{
            const btn = document.createElement('button')
            btn.textContent = ar.name
            btn.dataset.id = ar.id
            btn.addEventListener('click',()=>{selectArmario(ar.id)})
            armarioTabs.appendChild(btn)
        })
        // select first by default
        if(!armarioTabs.querySelector('button.active')){
            const first = armarioTabs.querySelector('button')
            if(first) first.click()
        }
        }

        let selectedArmarioId = null
        let selectedGavetaKey = 'T1'

        function selectArmario(id){
        selectedArmarioId = id
        Array.from(armarioTabs.querySelectorAll('button')).forEach(b=>b.classList.toggle('active', b.dataset.id===id))
        // default gaveta T1
        selectedGavetaKey='T1'
        renderArmarioTable()
        renderArmarioSummary()
        currentGaveta.textContent = selectedArmarioId + ' — '+selectedGavetaKey
        }

        function renderArmarioTable(){
        armarioTableBody.innerHTML=''
        const arm = state.armarios[selectedArmarioId]
        if(!arm) return
        // show all drawers: T1,T2,T3
        ['T1','T2','T3'].forEach(dk=>{
            const rows = arm.drawers[dk]
            if(rows.length===0){
            const tr = document.createElement('tr')
            tr.innerHTML = `<td>${dk}</td><td colspan="3"><em class="muted">(vazia)</em></td>`
            armarioTableBody.appendChild(tr)
            } else {
            rows.forEach((r, idx)=>{
                const piece = state.pieces.find(p=>p.id===r.pieceId) || {name:'-'}
                const tr = document.createElement('tr')
                tr.innerHTML = `<td>${dk}</td><td>${piece.name}</td><td>${r.qty}</td><td><button data-dk="${dk}" data-idx="${idx}" class="btn-danger small">REMOVER</button></td>`
                armarioTableBody.appendChild(tr)
            })
            }
        })

        // attach remove handlers for inline buttons
        armarioTableBody.querySelectorAll('button').forEach(b=>{
            b.addEventListener('click',()=>{
            const dk = b.dataset.dk; const idx = Number(b.dataset.idx)
            const row = state.armarios[selectedArmarioId].drawers[dk][idx]
            if(!row) return
            // remove that quantity from drawer and *add back* to presset? In this demo, 'REMOVER' means retirada para produção: decreases armario and logs movement (and reduces Presset total was previously decreased when moved to armario). To keep logic simple, this will remove from drawer and log movement
            const confirmed = confirm('Confirmar retirada de '+row.qty+' da gaveta '+dk+'?')
            if(!confirmed) return
            // Log movement
            pushLog({type:'retirada_gaveta', armario:selectedArmarioId, gaveta:dk, pieceId:row.pieceId, qty: row.qty, when: new Date().toISOString()})
            // remove
            state.armarios[selectedArmarioId].drawers[dk].splice(idx,1)
            save(); renderArmarioTable(); renderArmarioSummary(); renderPresset(); renderLog()
            })
        })

        currentGaveta.textContent = selectedArmarioId + ' — '+selectedGavetaKey
        }

        function renderArmarioSummary(){
        const arm = state.armarios[selectedArmarioId]
        if(!arm) return armarioSummary.textContent='-'
        const counts = {}
        Object.entries(arm.drawers).forEach(([dk,arr])=>{
            arr.forEach(it=>{ counts[it.pieceId]=(counts[it.pieceId]||0)+it.qty })
        })
        armarioSummary.innerHTML = Object.entries(counts).map(([pid,qty])=>{
            const p = state.pieces.find(x=>x.id===pid)
            return `<div>${p?p.name:pid}: ${qty} ${p?p.unit:''}</div>`
        }).join('') || '<em class="muted">(vazio)</em>'
        }

        function renderCadastroLists(){
        listsEl.innerHTML = `
            <div><strong>Peças:</strong> ${state.pieces.map(p=>p.name).join(', ')}</div>
            <div style="margin-top:8px"><strong>Funcionários:</strong> ${state.employees.join(', ') || '<em>(nenhum)</em>'}</div>
            <div style="margin-top:8px"><strong>Máquinas:</strong> ${state.machines.join(', ') || '<em>(nenhum)</em>'}</div>
            <div style="margin-top:8px"><strong>Fornecedores:</strong> ${state.suppliers.join(', ') || '<em>(nenhum)</em>'}</div>
        `
        }

        function renderLog(){
        logEl.innerHTML=''
        state.logs.slice().reverse().forEach(l=>{
            const p = state.pieces.find(x=>x.id===l.pieceId)
            const txt = (()=>{
            if(l.type==='to_armario') return `${formatDate(l.when)} — MOV. P/ ARMÁRIO ${l.armario} ${l.gaveta}: ${p?p.name:'?'} qde ${l.qty}`
            if(l.type==='from_armario') return `${formatDate(l.when)} — RETIRADA DA GAVETA ${l.armario} ${l.gaveta}: ${p?p.name:'?'} qde ${l.qty}`
            if(l.type==='presset_adjust') return `${formatDate(l.when)} — AJUSTE PRESSET: ${p?p.name:'?'} ${l.delta>0?'+':''}${l.delta}`
            if(l.type==='retirada_gaveta') return `${formatDate(l.when)} — RETIRADA (REMOVER) GAVETA ${l.armario} ${l.gaveta}: ${p?p.name:'?'} qde ${l.qty}`
            return JSON.stringify(l)
            })()
            const div = document.createElement('div');div.className='log-item';div.textContent = txt
            logEl.appendChild(div)
        })
        }

        function formatDate(iso){ const d = new Date(iso); return d.toLocaleString('pt-BR') }

        /* ====== Movimentações ====== */
        // Quando adiciona do Presset pra gaveta: decrementa Presset e adiciona na gaveta e loga
        toArmarioBtn.addEventListener('click',()=>{
        const pieceId = armarioPieceSelect.value
        const q = Number(armarioQty.value)||0
        if(!pieceId || q<=0) return alert('Escolha peça e quantidade > 0')
        // check presset qty
        const p = state.pieces.find(x=>x.id===pieceId)
        if(!p || p.qty < q) return alert('Quantidade em Presset insuficiente.')
        // add to selected gaveta
        const drawers = state.armarios[selectedArmarioId].drawers
        // if same piece exists in gaveta, sum
        const existing = drawers[selectedGavetaKey].find(it=>it.pieceId===pieceId)
        if(existing) existing.qty += q; else drawers[selectedGavetaKey].push({pieceId, qty:q})
        // decrement presset
        p.qty -= q
        pushLog({type:'to_armario', armario:selectedArmarioId, gaveta:selectedGavetaKey, pieceId, qty:q, when:new Date().toISOString()})
        save(); renderArmarioTable(); renderPresset(); renderArmarioSummary(); renderLog()
        })

        // Retirar da gaveta: coloca em produção (não retorna ao presset) e registra no log
        fromArmarioBtn.addEventListener('click',()=>{
        const pieceId = armarioPieceSelect.value
        const q = Number(armarioQty.value)||0
        if(!pieceId || q<=0) return alert('Escolha peça e quantidade > 0')
        const drawers = state.armarios[selectedArmarioId].drawers
        const existing = drawers[selectedGavetaKey].find(it=>it.pieceId===pieceId)
        if(!existing || existing.qty < q) return alert('Quantidade insuficiente na gaveta selecionada.')
        // reduce
        existing.qty -= q
        if(existing.qty===0){
            const idx = drawers[selectedGavetaKey].indexOf(existing); if(idx>=0) drawers[selectedGavetaKey].splice(idx,1)
        }
        pushLog({type:'from_armario', armario:selectedArmarioId, gaveta:selectedGavetaKey, pieceId, qty:q, when:new Date().toISOString()})
        save(); renderArmarioTable(); renderArmarioSummary(); renderLog(); renderPresset()
        })

        // When pressing ADD/REMOVE on Presset directly
        pressetAdd.addEventListener('click',()=>{
        const pieceId = pressetSelect.value; const q = Number(pressetQty.value)||0
        if(!pieceId||q<=0) return alert('Selecione peça e quantidade')
        const p = state.pieces.find(x=>x.id===pieceId); p.qty += q
        pushLog({type:'presset_adjust', pieceId, delta: q, when:new Date().toISOString()})
        save(); renderPresset(); renderLog()
        })
        pressetRemove.addEventListener('click',()=>{
        const pieceId = pressetSelect.value; const q = Number(pressetQty.value)||0
        if(!pieceId||q<=0) return alert('Selecione peça e quantidade')
        const p = state.pieces.find(x=>x.id===pieceId)
        if(p.qty < q) return alert('Quantidade insuficiente no Presset')
        p.qty -= q
        pushLog({type:'presset_adjust', pieceId, delta: -q, when:new Date().toISOString()})
        save(); renderPresset(); renderLog()
        })

        // When user clicks a table row to change selected gaveta
        armarioTableBody.addEventListener('click', (e)=>{
        const tr = e.target.closest('tr'); if(!tr) return
        const gaveta = tr.querySelector('td')?.textContent?.trim()
        if(['T1','T2','T3'].includes(gaveta)){
            selectedGavetaKey = gaveta
            currentGaveta.textContent = selectedArmarioId + ' — '+selectedGavetaKey
        }
        })

        // add piece
        addPieceBtn.addEventListener('click',()=>{
        const name = addPieceName.value.trim(); const unit = addPieceUnit.value.trim()||'un'
        if(!name) return alert('Nome da peça é obrigatório')
        const id = uid('p')
        state.pieces.push({id,name,unit,qty:0})
        addPieceName.value=''; addPieceUnit.value=''
        pushLog({type:'presset_adjust', pieceId:id, delta:0, when:new Date().toISOString(), note:'Cadastro'})
        save(); renderAll()
        })

        // generic push log
        function pushLog(obj){ state.logs.push(obj); if(state.logs.length>500) state.logs.shift(); save() }

        // small helpers for UI
        pressetSearch.addEventListener('input',()=>renderPresset())

        // initialize select for armarios and default selections
        function init(){
        // create default armario tabs
        renderAll()
        // set first armario active
        const firstId = Object.keys(state.armarios)[0]
        if(firstId) selectArmario(firstId)
        // click handlers for tabs already set in renderArmarioTabs
        }
        
        document.getElementById('formFerramenta').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const dados = {
                nome: form.nome.value,
                descricao: form.descricao.value || null,
                fabricante: form.fabricante.value || null
            };
            fetch('http://localhost:8000/armarios', {
                method: 'POST',
                headers: {pytho
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dados)
            })
            .then(response => response.json())
        });
        // expose quick debug: reset demo
        window.resetDemo = ()=>{ if(confirm('Resetar dados demo?')){ localStorage.removeItem(STORE_KEY); location.reload() } }

        init()
