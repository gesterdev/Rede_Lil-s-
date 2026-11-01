<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';
require_once 'header.php';

$mensagem = "";
$erro = "";

if (isset($_POST['recuperar'])) {
    $email = trim($_POST['email']);
    $nova_senha = trim($_POST['nova_senha']);

    if (!empty($email) && !empty($nova_senha)) {
        $check = mysqli_query($conn, "SELECT id FROM usuarios WHERE email = '$email'");
        if ($check && mysqli_num_rows($check) > 0) {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE usuarios SET senha = '$senha_hash' WHERE email = '$email'");
            $mensagem = "Senha redefinida com sucesso! Já pode fazer login.";
        } else {
            $erro = "E-mail não encontrado no sistema.";
        }
    } else {
        $erro = "Preencha todos os campos.";
    }
}
?>

 <div class="recuperar_senha">
    <main class="conteudo">
    <h2>Recuperar Senha</h2>

    <?php if (!empty($mensagem)): ?>
        <div class="mensagem-sucesso">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($erro)): ?>
        <div class="mensagem-erro">
            <?php echo $erro; ?>
        </div>
    <?php endif; ?>

    <form action="recuperar_senha.php" method="POST">
        <div class="campo-grupo">
            <label>Seu E-mail:</label>
            <input type="email" name="email" required placeholder="Digite seu e-mail cadastrado">
        </div>

        <div class="campo-grupo">
            <label>Nova Senha:</label>
            <input type="password" name="nova_senha" required placeholder="Digite a nova senha">
        </div>

        <button type="submit" name="recuperar" class="botao-principal">Redefinir Senha</button>
    </form>

    <div class="link_voltar">
        <a href="login.php">Voltar para o Login</a>
    </div>
    </main>
</div>

</body>
</html>

<?php 
require_once 'footer.php'; 
?>