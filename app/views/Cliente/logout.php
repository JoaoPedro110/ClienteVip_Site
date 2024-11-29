<?php
session_start();
session_destroy(); // Destruir a sessão

// Excluir cookie de CPF
setcookie('cpf', '', time() - 3600, "/"); // Definir tempo no passado

header('Location: ./login.php'); // Redirecionar para login
exit();
