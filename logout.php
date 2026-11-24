<?php
// Abre a sessão atual para apagar os dados do usuário e encerrar o acesso.
session_start();
session_unset();
session_destroy();

header('Location: index.php');
exit();