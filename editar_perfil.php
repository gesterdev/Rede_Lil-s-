<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php'; 

require_once 'auth.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$mensagem = "";


if (isset($_POST['atualizar'])) {
    $novo_nome = trim($_POST['nome']);
    $nova_senha = trim($_POST['senha']);

    if (!empty($novo_nome)) {
       
        $_SESSION['usuario_nome'] = $novo_nome;
        
        if (!empty($nova_senha)) {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nome = '$novo_nome', senha = '$senha_hash' WHERE id = $usuario_id";
        } else {
            $sql = "UPDATE usuarios SET nome = '$novo_nome' WHERE id = $usuario_id";
        }
        
        mysqli_query($conn, $sql);
        $mensagem = "Perfil atualizado com sucesso!";
    } else {
        $mensagem = "O nome não pode ficar vazio.";
    }
}


$res = mysqli_query($conn, "SELECT nome, email FROM usuarios WHERE id = $usuario_id");
$user = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

    <?php require_once 'header.php'; ?>

    <main class="conteudo" style="max-width: 500px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 8px; border: 1px solid #ddd;">
        <h2 style="color: #8a4baf; margin-top: 0;">Editar Meu Perfil</h2>

        <?php if (!empty($mensagem)): ?>
            <div style="background: #e2f0d9; color: #385723; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-weight: bold;">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <form action="perfil.php" method="POST">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; color: #555; margin-bottom: 5px;">E-mail (não pode ser alterado):</label>
                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="width: 100%; padding: 8px; background: #f5f5f5; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; color: #555; margin-bottom: 5px;">Nome:</label>
                <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; color: #555; margin-bottom: 5px;">Nova Senha (deixe em branco para não alterar):</label>
                <input type="password" name="senha" placeholder="Digite a nova senha se desejar" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>

            <button type="submit" name="atualizar" style="background: #8a4baf; color: white; border: none; padding: 10px 15px; border-radius: 4px; font-weight: bold; cursor: pointer; width: 100%;">Salvar Alterações</button>
        </form>

        <div style="margin-top: 20px; text-align: center;">
            <a href="rede.php" style="color: #8a4baf; text-decoration: none; font-size: 14px;">Voltar para a Rede Social</a>
        </div>
    </main>

</body>
</html>
<?php require_once 'footer.php'; ?>