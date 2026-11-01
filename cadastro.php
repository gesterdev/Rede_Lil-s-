<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $senha_pura = $_POST["senha"];
    
    if (!isset($_POST["declaracao"])) {
        $mensagem = "Você precisa confirmar a declaração.";
    } else {
        $declaracao = 1;
        $check_stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $mensagem = "Este e-mail já está cadastrado!";
            $check_stmt->close();
        } else {
            $check_stmt->close();
            $senha_hash = password_hash($senha_pura, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, declarou_ser_mulher) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $nome, $email, $senha_hash, $declaracao);

            if ($stmt->execute()) {
                $_SESSION['usuario_id'] = $stmt->insert_id;
                $_SESSION['usuario_nome'] = $nome;
                header("Location: rede_inicio.php");
                exit();
            } else {
                $mensagem = "Erro ao cadastrar: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Cadastro - Rede Lilás</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

    <?php require_once 'header.php'; ?>

    <div class="cadastro">
        <main class="conteudo">
            
            <h2>Criar Conta</h2>
            
            <?php if (!empty($mensagem)): ?>
                <p class="alerta-erro"><?php echo $mensagem; ?></p>
            <?php endif; ?>

            <form action="cadastro.php" method="POST">
                <label for="nome">Nome</label>
                <input type="text" name="nome" id="nome" placeholder="Seu nome" required>

                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="seu@email.com" required>

                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha" placeholder="Sua senha" required>

                <div class="termo-declaracao">
                    <label class="label-checkbox">
                        <input type="checkbox" name="declaracao" value="1" required>
                        <span>Declaro que sou mulher e estou ciente de que a Rede Lilás é destinada exclusivamente a mulheres.</span>
                    </label>
                </div>

                <button type="submit" class="botao-principal">
                    Cadastrar
                </button>
            </form>

            <p class="texto-alternativo">
                Já tem uma conta? <a href="login.php">Faça Login</a>
            </p>
        </main>
    </div>

</body>
</html>

<?php 
require_once 'footer.php'; 
?>