<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'header.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Comunidade - Rede Lilás</title>
    <link rel="stylesheet" href="index.css">
</head>
<body class="pagina-comunidade">

    <div class="comunidade-card">
        <main class="conteudo-comunidade">
            
            <h2>Comunidade Rede Lilás</h2>
            
            <p class="descricao-comunidade">
                Um espaço protegido e acolhedor dedicado exclusivamente a mulheres. Aqui você pode partilhar experiências, ler relatos de apoio mútuo e conectar-se com uma rede segura de solidariedade.
            </p>

            <div class="aviso-seguranca">
                <strong>Compromisso com a Segurança:</strong>
                <span>Para proteger a privacidade de todas as participantes, o acesso à comunidade é restrito a usuárias cadastradas. Suas interações e relatos são mantidos em total sigilo.</span>
            </div>

            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="rede.php" class="botao-principal-comunidade">
                    Acessar a Rede
                </a>
            <?php else: ?>
                <div class="botoes-acao">
                    <a href="login.php" class="botao-principal-comunidade">
                        Entrar na Conta
                    </a>
                    <a href="cadastro.php" class="botao-secundario-comunidade">
                        Criar Conta
                    </a>
                </div>
            <?php endif; ?>

        </main>
    </div>

</body>
</html>

<?php 
require_once 'footer.php'; 
?>