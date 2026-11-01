<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Esvazia o array da sessão
$_SESSION = array();

// Apaga o cookie da sessão do navegador, se existir
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destrói a sessão no servidor
session_destroy();

// Impede que o navegador armazene a página em cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redireciona para o login
header("Location: login.php");
exit();
?>