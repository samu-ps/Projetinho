from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
import mysql.connector

app = FastAPI()

# Permitir requisições do seu site
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Troque por seu domínio em produção
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

def get_connection():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="streparavadb"
    )

@app.get("/armarios")
def listar_armarios():
    conn = get_connection()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT * FROM armario")
    armarios = cursor.fetchall()
    cursor.close()
    conn.close()
    return armarios

@app.get("/armarios/linha/{linha}")
def listar_armarios_por_linha(linha: str):
    conn = get_connection()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT * FROM armario WHERE linha = %s", (linha,))
    armarios = cursor.fetchall()
    cursor.close()
    conn.close()
    return armarios

# Exemplo para cadastrar um armário
from pydantic import BaseModel

class Armario(BaseModel):
    turno: str
    linha: str
    funcionario_id: int = None
    qtd_prevista: int = None

@app.post("/armarios")
def criar_armario(armario: Armario):
    conn = get_connection()
    cursor = conn.cursor()
    cursor.execute(
        "INSERT INTO armario (turno, linha, funcionario_id, qtd_prevista) VALUES (%s, %s, %s, %s)",
        (armario.turno, armario.linha, armario.funcionario_id, armario.qtd_prevista)
    )
    conn.commit()
    cursor.close()
    conn.close()
    return {"status": "Armário cadastrado com sucesso"}

@app.get("/armarios/ultimo_id")
def ultimo_id_armario():
    conn = get_connection()
    cursor = conn.cursor()
    cursor.execute("SELECT MAX(id) FROM armario")
    ultimo_id = cursor.fetchone()[0]
    cursor.close()
    conn.close()
    proximo_id = (ultimo_id or 0) + 1
    return {"proximo_id": proximo_id}

@app.delete("/armarios/{id}")
def deletar_armario(id: int):
    conn = get_connection()
    cursor = conn.cursor()
    cursor.execute("DELETE FROM armario WHERE id = %s", (id,))
    conn.commit()
    cursor.close()
    conn.close()
    return {"status": "Armário deletado com sucesso"}

# Cadastrar ferramentas
class Ferramenta(BaseModel):
    nome: str
    descricao: str
    vida_util: str
    qtd_estoque: str

@app.get("/ferramentas")
def listar_ferramentas():
    conn = get_connection()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT * FROM ferramentas")
    ferramentas = cursor.fetchall()
    cursor.close()
    conn.close()
    return ferramentas

@app.post("/ferramentas")
def criar_ferramenta(ferramentas: Ferramenta):
    conn = get_connection()
    cursor = conn.cursor()
    cursor.execute(
        "INSERT INTO ferramentas (nome, descricao, vida_util, qtd_estoque) VALUES (%s, %s, %s, %s)",
        (ferramentas.nome, ferramentas.descricao, ferramentas.vida_util, ferramentas.qtd_estoque)
    )
    conn.commit()
    cursor.close()
    conn.close()
    return {"status": "Armário cadastrado com sucesso"}

@app.delete("/ferramentas/{id}")
def deletar_ferramenta(id: int):
    conn = get_connection()
    cursor = conn.cursor()
    cursor.execute("DELETE FROM ferramentas WHERE id = %s", (id,))
    conn.commit()
    cursor.close()
    conn.close()
    return {"status": "Ferramenta deletado com sucesso"}


# Endpoints simples para salvar/ler relatórios em um arquivo local (db.txt)
from pathlib import Path

BASE_DIR = Path(__file__).resolve().parent
REL_FILE = BASE_DIR / "relatorios.jsonl"


class Relatorio(BaseModel):
    texto: str


from datetime import datetime
import json
import uuid


@app.get("/relatorio")
def get_relatorio():
    """Retorna lista de relatórios (cada linha é um JSON)."""
    items = []
    migrated = False
    if REL_FILE.exists():
        with REL_FILE.open("r", encoding="utf-8") as f:
            for line in f:
                line = line.strip()
                if not line:
                    continue
                try:
                    obj = json.loads(line)
                except Exception:
                    # pular linhas inválidas
                    continue
                if "id" not in obj:
                    obj["id"] = str(uuid.uuid4())
                    migrated = True
                items.append(obj)

    # se migramos, reescrevemos o arquivo com os ids adicionados
    if migrated:
        with REL_FILE.open("w", encoding="utf-8") as f:
            for it in items:
                f.write(json.dumps(it, ensure_ascii=False) + "\n")

    return {"relatorios": items}


@app.post("/relatorio")
def post_relatorio(rel: Relatorio):
    """Recebe JSON {"texto": "..."}, anexa um bloco JSON com timestamp ao arquivo e retorna o item criado."""
    REL_FILE.parent.mkdir(parents=True, exist_ok=True)
    item = {"id": str(uuid.uuid4()), "texto": rel.texto.strip(), "timestamp": datetime.utcnow().isoformat()}
    with REL_FILE.open("a", encoding="utf-8") as f:
        f.write(json.dumps(item, ensure_ascii=False) + "\n")
    return {"status": "ok", "item": item}



@app.delete("/relatorio/{item_id}")
def delete_relatorio(item_id: str):
    """Remove o relatório com id == item_id regravando o arquivo sem ele."""
    if not REL_FILE.exists():
        return {"status": "not_found"}
    items = []
    removed = False
    with REL_FILE.open("r", encoding="utf-8") as f:
        for line in f:
            line = line.strip()
            if not line:
                continue
            try:
                obj = json.loads(line)
            except Exception:
                continue
            if str(obj.get("id")) == str(item_id):
                removed = True
                continue
            items.append(obj)

    # reescrever o arquivo com os itens restantes
    with REL_FILE.open("w", encoding="utf-8") as f:
        for it in items:
            f.write(json.dumps(it, ensure_ascii=False) + "\n")

    return {"status": "deleted" if removed else "not_found"}

    @app.get("/")
    def hello():
        return {"msg": "ok"}