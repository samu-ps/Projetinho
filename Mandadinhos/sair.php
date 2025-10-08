<?php
    session_start();
    session_unset(); // Limpa variaveis
    session_destroy(); //Apaga variaveis
    header('location:index.php'); //
    exit() //
?>