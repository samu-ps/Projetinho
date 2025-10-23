<?php
    session_start();
    require_once("./conexao.php");

    $user = isset($_SESSION['user']) ? $_SESSION['user'] : 'Visitante';
?>

<div class="header">
    <div class="header-left">
        <img src="./img/Streparava.png" alt="Logo Streparava" class="logo">
    </div>

    <div class="header-right">
        <span class="bemvindo">
            <span class="saudacao">Bem-vindo,</span>
            <strong><?php echo htmlspecialchars($user); ?></strong>
            <button class="usuario-btn">
                <img src="./img/Helik.png" alt="" width="40px" >
            </button>
        </span>
    </div>
</div>

<!-- Modal de sair -->
<div id="modalSair" class="modal">
    <div class="modal-content">
        <h3>Deseja sair?</h3>
        <form action="sair.php" method="POST">
            <button type="submit" class="btn-confirmar">Sair</button>
            <button type="button" class="btn-cancelar" id="cancelarSair">Cancelar</button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("modalSair");
    const btnUsuario = document.querySelector(".usuario-btn");
    const btnCancelar = document.getElementById("cancelarSair");

    // Mostrar modal ao clicar no botão do usuário
    btnUsuario.addEventListener("click", () => {
        modal.style.display = "flex"; // usa flex para alinhar no centro
    });

    // Fechar modal ao clicar em cancelar
    btnCancelar.addEventListener("click", () => {
        modal.style.display = "none";
    });

    // Fechar modal ao clicar fora do conteúdo
    modal.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });
});
</script>
