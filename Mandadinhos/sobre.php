<!-- Estrutura php -->
<?php require_once('./conexao.php'); 
// Criar uma sessão para compartilhar as variaveis de memoria @session_start(); ?>
<!-- Estrutura html -->
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- 2-Editar a estrutura padrão: titúlo,css,icone -->
    <!-- <link rel="stylesheet" href=".\css\estilos.css"> -->
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <title>Login</title>
</head>

<body>
    <!-- 3-Criar a DIV e o FORM - body{} -->

    <div class="header">
        <img src="./img/Streparava.png" alt="logo streparava">
    </div>
    
    <!-- Criar formulário -->
    <!-- 3.1 -3.2 inputs e button -->

    <div class="container sobre-container">
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
    <footer class="sobre-footer">
            <p>© 2025 Sistema de Gerenciamento Streparava - Todos os direitos reservados</p>
        </footer>
</body>

</html>