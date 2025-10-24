from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import mysql.connector
from pathlib import Path
from datetime import datetime
import json
import uuid
import os

# -----------------------------
# Configuração FastAPI e CORS
# -----------------------------
app = FastAPI(title="Gestão de Armários e Ferramentas")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Para teste; trocar para seu domínio em produção
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# -----------------------------
# Conexão MySQL
# -----------------------------
def get_connection():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="",  # sua senha
        database="streparavadb"
    )

# -----------------------------
# Models
# -----------------------------
class Armario(BaseModel):
    turno: str
    linha: str
    funcionario_id: int = None
    qtd_prevista: int = None

class Ferramenta(BaseModel):
    nome: str
    descricao: str
    vida_util: str
    qtd_estoque: int


class FerramentaUpdate(BaseModel):
    qtd_estoque: int

class FerramentaTransfer(BaseModel):
    nome: str
    id_ferramenta: int
    linha: str
    qtd_transferida: int
    turno: str

class Relatorio(BaseModel):
    texto: str

# Arquivo de relatórios
BASE_DIR = Path(__file__).resolve().parent
REL_FILE = BASE_DIR / "relatorios.jsonl"

# -----------------------------
# Rotas Armários
# -----------------------------
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
def get_armarios_por_linha(linha: str):
    conn = get_connection()
    cursor = conn.cursor(dictionary=True)

    # Busca os armários e as ferramentas associadas
    cursor.execute("""
        SELECT 
            a.id AS armario_id,
            a.linha,
            a.turno,
            a.qtd_estoque AS qtd_armario,
            f.id AS ferramenta_id,
            f.nome AS ferramenta_nome,
            f.qtd_estoque AS qtd_total_ferramenta
        FROM armario a
        LEFT JOIN ferramentas f ON a.id_ferramenta = f.id
        WHERE a.linha = %s
        ORDER BY a.turno;
    """, (linha,))

    registros = cursor.fetchall()

    # Agrupar por turno
    armarios = {}
    for r in registros:
        turno = r["turno"] or "Desconhecido"
        if turno not in armarios:
            armarios[turno] = []

        armarios[turno].append({
            "id": r["armario_id"],
            "linha": r["linha"],
            "turno": r["turno"],
            "ferramentas": [{
                "id": r["ferramenta_id"],
                "nome": r["ferramenta_nome"],
                "qtd_estoque": r["qtd_armario"]
            }]
        })

    cursor.close()
    conn.close()

    # Retornar como lista (o frontend espera isso)
    resultado = []
    for turno, dados in armarios.items():
        resultado.extend(dados)

    return resultado

@app.get("/armarios/ultimo_id")
def ultimo_id_armario():
    conn = get_connection()
    cursor = conn.cursor()
    cursor.execute("SELECT MAX(id) FROM armario")
    ultimo_id = cursor.fetchone()[0]
    cursor.close()
    conn.close()
    return {"proximo_id": (ultimo_id or 0) + 1}

# @app.post("/armarios")
# def criar_armario(armario: Armario):
#     conn = get_connection()
#     cursor = conn.cursor()
#     cursor.execute(
#         "INSERT INTO armario (turno, linha, funcionario_id, qtd_prevista) VALUES (%s, %s, %s, %s)",
#         (armario.turno, armario.linha, armario.funcionario_id, armario.qtd_prevista)
#     )
#     conn.commit()
#     cursor.close()
#     conn.close()
#     return {"status": "Armário cadastrado com sucesso"}

@app.delete("/armarios/{id}")
def deletar_armario(id: int):
    conn = get_connection()
    cursor = conn.cursor()
    cursor.execute("DELETE FROM armario WHERE id = %s", (id,))
    conn.commit()
    cursor.close()
    conn.close()
    return {"status": "Armário deletado com sucesso"}

# -----------------------------
# Rotas Ferramentas
# -----------------------------
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
def criar_ferramenta(ferramenta: Ferramenta):
    conn = get_connection()
    cursor = conn.cursor()
    cursor.execute(
        "INSERT INTO ferramentas (nome, descricao, vida_util, qtd_estoque) VALUES (%s, %s, %s, %s)",
        (ferramenta.nome, ferramenta.descricao, ferramenta.vida_util, ferramenta.qtd_estoque)
    )
    conn.commit()
    cursor.close()
    conn.close()
    return {"status": "Ferramenta cadastrada com sucesso"}

@app.delete("/ferramentas/{id}")
def deletar_ferramenta(id: int):
    conn = get_connection()
    cursor = conn.cursor()
    cursor.execute("DELETE FROM ferramentas WHERE id = %s", (id,))
    conn.commit()
    cursor.close()
    conn.close()
    return {"status": "Ferramenta deletada com sucesso"}

@app.put("/ferramentas/{id}")
def atualizar_qtd_estoque(id: int, dados: FerramentaUpdate):
    conn = get_connection()
    cursor = conn.cursor()
    cursor.execute("UPDATE ferramentas SET qtd_estoque = %s WHERE id = %s", (dados.qtd_estoque, id))
    conn.commit()
    if cursor.rowcount == 0:
        cursor.close()
        conn.close()
        raise HTTPException(status_code=404, detail="Ferramenta não encontrada")
    cursor.close()
    conn.close()
    return {"mensagem": "Quantidade de estoque atualizada com sucesso"}

# -----------------------------
# Transferência Presset -> Armário
# -----------------------------
@app.post("/transferir_presset_para_armario")
def transferir_presset_para_armario(dados: FerramentaTransfer):
    conn = get_connection()
    cursor = conn.cursor(dictionary=True)

    # 1️⃣ Buscar ferramenta
    cursor.execute("SELECT * FROM ferramentas WHERE id = %s", (dados.id_ferramenta,))
    ferramenta = cursor.fetchone()
    if not ferramenta:
        cursor.close()
        conn.close()
        raise HTTPException(status_code=404, detail=f"Ferramenta com ID {dados.id_ferramenta} não encontrada")

    # 2️⃣ Verificar se já existe no armário (mesma linha + turno + ferramenta)
    cursor.execute("""
        SELECT * FROM armario
        WHERE linha = %s AND turno = %s AND id_ferramenta = %s
    """, (dados.linha, dados.turno, dados.id_ferramenta))
    existente = cursor.fetchone()

    if existente:
        # Atualiza quantidade (pode ser positiva ou negativa)
        nova_qtd = max(0, int(existente["qtd_estoque"]) + dados.qtd_transferida)
        cursor.execute("""
            UPDATE armario
            SET qtd_estoque = %s, nome = %s
            WHERE id = %s
        """, (nova_qtd, dados.nome, existente["id"]))
    else:
        # Cria novo registro no armário
        cursor.execute("""
            INSERT INTO armario (id_ferramenta, nome, linha, turno, qtd_estoque)
            VALUES (%s, %s, %s, %s, %s)
        """, (dados.id_ferramenta, dados.nome, dados.linha, dados.turno, dados.qtd_transferida))

    # 3️⃣ Atualizar estoque da tabela ferramentas APENAS se for saída (transferência positiva)
    if dados.qtd_transferida > 0:
        cursor.execute("""
            UPDATE ferramentas
            SET qtd_estoque = qtd_estoque - %s
            WHERE id = %s
        """, (dados.qtd_transferida, dados.id_ferramenta))
        # Evita valores negativos
        cursor.execute("""
            UPDATE ferramentas
            SET qtd_estoque = 0
            WHERE qtd_estoque < 0
        """)

    conn.commit()
    cursor.close()
    conn.close()

    # Mensagem amigável
    acao = "adicionada" if dados.qtd_transferida > 0 else "retirada"
    return {
        "mensagem": f"Ferramenta '{dados.nome}' {acao} com sucesso no armário da linha {dados.linha} ({dados.turno})."
    }
# -----------------------------
# Rotas de Relatórios (arquivo JSON)
# -----------------------------
class Relatorio(BaseModel):
    texto: str

ARQUIVO = "relatorios.jsonl"

@app.post("/salvar_relatorio")
def salvar_relatorio(rel: Relatorio):
    """Salva o relatório no arquivo JSONL"""
    dado = {"data": datetime.now().strftime("%d-%m-%Y / %H:%M:%S"), "texto": rel.texto}
    with open(ARQUIVO, "a", encoding="utf-8") as f:
        f.write(json.dumps(dado, ensure_ascii=False) + "\n")
    return {"status": "ok", "mensagem": "Relatório salvo."}

@app.get("/relatorios")
def listar_relatorios():
    """Lê todos os relatórios salvos"""
    if not os.path.exists(ARQUIVO):
        return []
    with open(ARQUIVO, "r", encoding="utf-8") as f:
        linhas = [json.loads(l) for l in f if l.strip()]
    return linhas