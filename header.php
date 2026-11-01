<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';
$pagina_atual = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rede Lilás</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

<header class="cabecalho">
    <div class="logo">
        <h1><a href="interface.php">Rede Lilás</a></h1>
    </div>
<nav class="menu">
        <a href="informacoes.php">Informações</a>

        <?php if ($pagina_atual !== 'login.php' && $pagina_atual !== 'cadastro.php' && $pagina_atual !== 'interface.php'): ?>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="editar_perfil.php" style="color: #fff; font-weight: bold;">Meu Perfil</a>
                <a href="mensagens.php" style="color: #fff; font-weight: bold;">Mensagens</a>
                
                <?php 
                if (!isset($conn)) { require_once 'db.php'; }
                $uid_logado = $_SESSION['usuario_id'];
                $sql_admin_check = @mysqli_query($conn, "SELECT is_admin FROM usuarios WHERE id = $uid_logado");
                if ($sql_admin_check && $dados_user = mysqli_fetch_assoc($sql_admin_check)) {
                    if (isset($dados_user['is_admin']) && $dados_user['is_admin'] == 1) {
                        echo '<a href="admin.php" style="color: #ffeb3b; font-weight: bold;">Painel Admin</a>';
                    }
                }
                ?>
                <a href="logout.php">Sair</a>
            <?php endif; ?>
        <?php else: ?>
            <?php if ($pagina_atual === 'interface.php' && !isset($_SESSION['usuario_id'])): ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        <?php endif; ?>
        
        <button id="btn-panico" class="btn-panico"> SAÍDA RÁPIDA (PÂNICO) </button>
        
    </nav>
    
</header>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btnPanico = document.getElementById('btn-panico');
        if (btnPanico) {
            btnPanico.addEventListener("click", function(e) {
                e.preventDefault();
                window.location.replace("login.php");
            });
        }
    });
</script>
</body>
</html>