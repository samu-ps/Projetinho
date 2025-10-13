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

@app.get("/ferramentas")
def listar_armarios():
    conn = get_connection()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT * FROM ferramentas")
    armarios = cursor.fetchall()
    cursor.close()
    conn.close()
    return armarios
    
from pydantic import BaseModel

class Ferramentas(BaseModel):
    nome: str
    descricao: str
    fabricante: str

@app.post("/ferramentas")
def criar_ferramenta(ferramentas: Ferramentas):
    try:
        conn = get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "INSERT INTO ferramentas (nome, descricao, fabricante) VALUES (%s, %s, %s)",
            (ferramentas.nome, ferramentas.descricao, ferramentas.fabricante)
        )
        conn.commit()
        cursor.close()
        conn.close()
        return {"status": "Ferramenta cadastrada com sucesso"}
    except Exception as e:
        # retornar erro detalhado para debug (não exponha assim em produção)
        try:
            # tentar fechar conexões se abertas
            cursor.close()
        except Exception:
            pass
        try:
            conn.close()
        except Exception:
            pass
        return {"status": "error", "detail": str(e)}


@app.get('/health')
def health():
    """Verifica conexão simples com o banco e retorna nome do DB ou erro."""
    try:
        conn = get_connection()
        cursor = conn.cursor()
        cursor.execute('SELECT DATABASE()')
        db = cursor.fetchone()[0]
        cursor.close()
        conn.close()
        return {"ok": True, "database": db}
    except Exception as e:
        return {"ok": False, "error": str(e)}