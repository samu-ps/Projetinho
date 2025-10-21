<div class="header">
    <div class="header-left">
        <img src="./img/Streparava.png" alt="Logo Streparava" class="logo">
    </div>

    <div class="header-right">
        <span class="bemvindo">
            <span class="saudacao">Bem-vindo,</span>
            <strong><?php echo htmlspecialchars($usuario); ?></strong>
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
