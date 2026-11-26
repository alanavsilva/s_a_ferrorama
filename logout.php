<?php
// Ao confirmar a saída, o PHP encerra a sessão e volta para a tela de entrada.

// Quando você clica em Sair, este arquivo abre a mesma sessão usada no login.
session_start();

//Limpa o conteúdo de $_SESSION
session_unset();

// Encerra a sessão que acabamos de esvaziar.
session_destroy();

// Volta para a tela de login depois de sair.
header('Location: index.php');

// Termina o código do logout aqui.
exit();
