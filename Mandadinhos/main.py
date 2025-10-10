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
        database="streparava"
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