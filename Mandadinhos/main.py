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

# Exemplo para cadastrar um armário
from pydantic import BaseModel

class Armario(BaseModel):
    id: int
    turno: str
    linha: str
    funcionario_id: int = None
    qtd_prevista: int = None

@app.post("/armarios")
def criar_armario(armario: Armario):
    conn = get_connection()
    cursor = conn.cursor()
    cursor.execute(
        "INSERT INTO armario (id, turno, linha, funcionario_id, qtd_prevista) VALUES (%s, %s, %s, %s, %s)",
        (armario.id, armario.turno, armario.linha, armario.funcionario_id, armario.qtd_prevista)
    )
    conn.commit()
    cursor.close()
    conn.close()
    return {"status": "Armário cadastrado com sucesso"}