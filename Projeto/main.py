from fastapi import FastAPI, Request, Form
from fastapi.responses import HTMLResponse, FileResponse, JSONResponse
from fastapi.staticfiles import StaticFiles
from fastapi.middleware.cors import CORSMiddleware
import mysql.connector
from mysql.connector import Error
from pathlib import Path

# =======================
# CONFIGURAÇÕES INICIAIS
# =======================
BASE_DIR = Path(__file__).resolve().parent
app = FastAPI(title="Streparava - Controle de Ferramentas")

# Permitir chamadas do front-end (JS)
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Servir arquivos estáticos (CSS, JS)
app.mount("/static", StaticFiles(directory=BASE_DIR), name="static")

# =======================
# CONEXÃO COM O BANCO
# =======================
def get_connection():
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",    
            database="streparavadb"
        )
        return conn
    except Error as e:
        print("Erro ao conectar no banco:", e)
        return None


# =======================
# ROTAS FRONT-END
# =======================
@app.get("/", response_class=HTMLResponse)
async def root():
    return FileResponse(BASE_DIR / "Index.html")


@app.get("/style.css")
async def css():
    return FileResponse(BASE_DIR / "style.css")


@app.get("/script.js")
async def js():
    return FileResponse(BASE_DIR / "script.js")


# =======================
# ROTAS API (JSON)
# =======================

# ---- Ferramentas ----
@app.get("/api/ferramentas")
async def listar_ferramentas():
    conn = get_connection()
    cur = conn.cursor(dictionary=True)
    cur.execute("SELECT * FROM ferramentas")
    dados = cur.fetchall()
    cur.close(); conn.close()
    return JSONResponse(dados)

@app.post("/api/ferramentas")
async def adicionar_ferramenta(
    nome: str = Form(...),
    cod: str = Form(""),
    descricao: str = Form(""),
    vida_util: int = Form(0),
    qtd_estoque: int = Form(0),
    fabricante: str = Form(""),
    preco_unitario: float = Form(0.0),
    data_aquisicao: str = Form(None),
    localizacao: str = Form("")
):
    conn = get_connection()
    cur = conn.cursor()
    sql = """
        INSERT INTO ferramentas (nome, cod, descricao, vida_util, qtd_estoque, fabricante, preco_unitario, data_aquisicao, localizacao)
        VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)
    """
    cur.execute(sql, (nome, cod, descricao, vida_util, qtd_estoque, fabricante, preco_unitario, data_aquisicao, localizacao))
    conn.commit()
    cur.close(); conn.close()
    return {"mensagem": "Ferramenta adicionada com sucesso!"}

# ---- Funcionários ----
@app.get("/api/funcionarios")
async def listar_funcionarios():
    conn = get_connection()
    cur = conn.cursor(dictionary=True)
    cur.execute("""
        SELECT f.id, f.nome, f.matricula, d.nome AS departamento, f.funcao, f.contato, f.status
        FROM funcionarios f
        LEFT JOIN departamento d ON f.departamento_id = d.id
    """)
    dados = cur.fetchall()
    cur.close(); conn.close()
    return JSONResponse(dados)

@app.post("/api/funcionarios")
async def adicionar_funcionario(
    nome: str = Form(...),
    matricula: str = Form(""),
    departamento_id: int = Form(None),
    funcao: str = Form(""),
    contato: str = Form(""),
    status: str = Form("Ativo")
):
    conn = get_connection()
    cur = conn.cursor()
    cur.execute("""
        INSERT INTO funcionarios (matricula, nome, departamento_id, funcao, contato, status)
        VALUES (%s,%s,%s,%s,%s,%s)
    """, (matricula, nome, departamento_id, funcao, contato, status))
    conn.commit()
    cur.close(); conn.close()
    return {"mensagem": "Funcionário cadastrado com sucesso!"}


# ---- Armários ----
@app.get("/api/armarios")
async def listar_armarios():
    conn = get_connection()
    cur = conn.cursor(dictionary=True)
    cur.execute("""
        SELECT a.id, a.turno, a.linha, f.nome AS funcionario, a.qtd_prevista
        FROM armario a
        LEFT JOIN funcionarios f ON a.funcionario_id = f.id
    """)
    dados = cur.fetchall()
    cur.close(); conn.close()
    return JSONResponse(dados)


# =======================
# TESTE RÁPIDO
# =======================
@app.get("/api/teste")
async def teste():
    return {"status": "ok", "mensagem": "API funcionando com sucesso!"}


# =======================
# EXECUÇÃO LOCAL
# =======================
# uvicorn main:app --reload
