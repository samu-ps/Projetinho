<style>
.sobre-container {
  background-color: #ffffff;
  color: #cc0000;
  font-family: "Segoe UI", Arial, sans-serif;
  padding: 60px 80px;
  /* display: flex; */
  flex-direction: column;
  align-items: flex-start;
  gap: 40px; /* espaço entre as seções */
  width: 100%;
  box-sizing: border-box;
}

/* Cada seção ocupa toda a largura e fica uma abaixo da outra */
.sobre-section {
  width: 100%;
  display: flex;
  flex-direction: column;
}

/* Títulos */
.sobre-section h2 {
  color: #cc0000;
  font-size: 1.8rem;
  border-bottom: 3px solid #cc0000;
  padding-bottom: 5px;
  margin-bottom: 15px;
  width: 100%;
}

/* Parágrafos */
.sobre-section p {
  color: #333;
  font-size: 1rem;
  line-height: 1.7;
  text-align: justify;
  margin-bottom: 10px;
  width: 100%;
}

/* Lista */
.sobre-section ul {
  list-style-type: disc;
  margin-left: 30px;
  color: #333;
}

/* Footer ocupa toda a largura da seção sobre */
.sobre-footer {
  background-color: #cc0000;
  color: white;
  text-align: center;
  padding: 40px 20px;
  border-radius: 0;
  font-size: 1rem;
  font-weight: 500;
  width: 100%;
  margin-top: 40px;
  box-shadow: 0 -3px 8px rgba(0, 0, 0, 0.1);
}

/* Texto do footer */
.sobre-footer p {
  margin: 0;
  width: 100%;
  text-align: center;
}

/* Responsividade */
@media (max-width: 768px) {
  .sobre-container {
    padding: 30px 20px;
  }

  .sobre-section h2 {
    font-size: 1.4rem;
  }

  .sobre-footer {
    padding: 30px 15px;
    font-size: 0.9rem;
  }
}
</style>

<div class="sobre-container">
    <section class="sobre-section">
        <h2>Visão Geral</h2>
        <p>O Sistema de Gerenciamento Streparava é uma solução inovadora desenvolvida com dedicação e expertise pelos alunos dos 2º e 3º módulos do curso Técnico em Informática do ano de 2025. Este projeto representa não apenas uma ferramenta de gestão, mas também um importante marco no aprendizado e desenvolvimento profissional dos estudantes envolvidos.</p>
    </section>

    <section class="sobre-section">
        <h2>Objetivo do Projeto</h2>
        <p>O sistema foi criado para aprendizado e uso prático, com o objetivo de otimizar e modernizar a gestão de estoque da empresa Streparava. Com uma interface intuitiva e funções robustas, permite um controle mais eficiente e preciso do inventário, aumentando a eficiência operacional.</p>
    </section>

    <section class="sobre-section">
        <h2>Desenvolvido por</h2>
        <p>Este projeto é resultado do esforço colaborativo entre:</p>
        <ul>
            <li>Alunos do 2º Módulo de Informática (2025)</li>
            <li>Alunos do 3º Módulo de Informática (2025)</li>
        </ul>
    </section>

    <section class="sobre-section">
        <h2>Informações Técnicas</h2>
        <p><strong>Versão:</strong> 1.0</p>
        <p><strong>Data de Lançamento:</strong> 05 de Novembro de 2025</p>
    </section>

    <section class="sobre-section">
        <h2>Agradecimentos</h2>
        <p>Agradecemos à empresa Streparava pela oportunidade de desenvolver este projeto, aos professores pelo suporte e orientação, e a todos os alunos que contribuíram com seu tempo, dedicação e criatividade para tornar este sistema uma realidade.</p>
    </section>
</div>
