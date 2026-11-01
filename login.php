<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

session_start();
require_once 'db.php';

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tipo_acao']) && $_POST['tipo_acao'] == 'entrar') {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (!empty($email) && !empty($senha)) {
        $stmt = $conn->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
            if (password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                header("Location: rede_inicio.php");
                exit();
            } else {
                $mensagem = "Palavra-passe incorreta.";
            }
        } else {
            $mensagem = "Não existe nenhuma conta com este email.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <title>Login - Rede Lilás</title>
    <link rel="stylesheet" href="index.css">
    <script>
        // Trava o botão "Voltar" do navegador na tela de login
        history.pushState(null, "", location.href);
        window.addEventListener('popstate', function (event) {
            history.pushState(null, "", location.href);
            window.location.replace("login.php");
        });
    </script>
</head>
<body class="pagina-login">

    <div class="login">
        <?php if (!empty($mensagem)): ?>
            <p class="alerta-erro"><?php echo $mensagem; ?></p>
        <?php endif; ?>

        <h2>Login</h2>
        
        <form action="login.php" method="POST">
            <input type="hidden" name="tipo_acao" value="entrar">

            <label for="email">Email</label>
            <input type="email" name="email" id="email" autocomplete="off" required>

            <label for="senha">Senha</label>
            <input type="password" name="senha" id="senha" autocomplete="new-password" autocomplete="off" required>

            <div class="link_senha">
                <a href="recuperar_senha.php">Esqueceu a sua senha?</a>
            </div>

            <button type="submit" class="botao-principal">Entrar</button>
        </form>

        <p class="texto-alternativo">
            Não tem uma conta? <a href="cadastro.php">Registre-se</a>
        </p>
    </div>

</body>
</html>