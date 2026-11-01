<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php'; 
require_once 'auth.php';
require_once 'header.php';


if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_logado_id = $_SESSION['usuario_id'];

$chk_admin = mysqli_query($conn, "SELECT is_admin FROM usuarios WHERE id = $usuario_logado_id");
$dados_admin = mysqli_fetch_assoc($chk_admin);

if (!$dados_admin || $dados_admin['is_admin'] != 1) {
    echo "<script>alert('Acesso negado! Apenas administradoras podem aceder a esta página.'); window.location='rede.php';</script>";
    exit();
}

$mensagem = "";


if (isset($_POST['alterar_senha'])) {
    $alvo_id = intval($_POST['usuario_id']);
    $nova_senha_pura = trim($_POST['nova_senha']);
    
    if (!empty($nova_senha_pura)) {
        $nova_senha_hash = password_hash($nova_senha_pura, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE usuarios SET senha = '$nova_senha_hash' WHERE id = $alvo_id");
        $mensagem = "Senha da usuária alterada com sucesso!";
    }
}


if (isset($_GET['deletar_usuario'])) {
    $alvo_id = intval($_GET['deletar_usuario']);
    
    if ($alvo_id != $usuario_logado_id) {
        mysqli_query($conn, "DELETE FROM publicacoes WHERE usuario_id = $alvo_id");
        mysqli_query($conn, "DELETE FROM comentarios WHERE usuario_id = $alvo_id");
        mysqli_query($conn, "DELETE FROM curtidas WHERE usuario_id = $alvo_id");
        mysqli_query($conn, "DELETE FROM usuarios WHERE id = $alvo_id");
        $mensagem = "Conta de usuária removida com sucesso!";
    }
}


if (isset($_GET['deletar_pub'])) {
    $pub_id = intval($_GET['deletar_pub']);
    
    $verifica = mysqli_query($conn, "SELECT arquivo_path FROM publicacoes WHERE id = $pub_id");
    if ($verifica && mysqli_num_rows($verifica) > 0) {
        $dados_pub = mysqli_fetch_assoc($verifica);
        if (!empty($dados_pub['arquivo_path']) && file_exists($dados_pub['arquivo_path'])) {
            unlink($dados_pub['arquivo_path']);
        }
        mysqli_query($conn, "DELETE FROM curtidas WHERE publicacao_id = $pub_id");
        mysqli_query($conn, "DELETE FROM comentarios WHERE publicacao_id = $pub_id");
        mysqli_query($conn, "DELETE FROM publicacoes WHERE id = $pub_id");
        $mensagem = "Publicação moderada/excluída com sucesso!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

    <?php require_once 'header.php'; ?>

    <main class="conteudo" style="max-width: 800px; margin: 30px auto; padding: 20px; background: #fff; border-radius: 8px; border: 1px solid #ddd;">
        
        <h2 style="color: #8a4baf; border-bottom: 2px solid #8a4baf; padding-bottom: 10px;">🛡️ Painel Administrativo e Moderação</h2>
        
        <?php if (!empty($mensagem)): ?>
            <div style="background: #e2f0d9; color: #385723; padding: 10px; border-radius: 5px; margin: 15px 0; font-weight: bold;">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

    
        <section style="margin-top: 30px;">
            <h3 style="color: #333;">Gerir Cadastros de Usuárias</h3>
            <p style="font-size: 14px; color: #666;">Aqui pode visualizar todas as contas registadas, redefinir a senha caso uma usuária perca o acesso, ou remover contas indevidas.</p>
            
            <table style="width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 14px;">
                <thead>
                    <tr style="background-color: #8a4baf; color: white; text-align: left;">
                        <th style="padding: 10px;">ID</th>
                        <th style="padding: 10px;">Nome</th>
                        <th style="padding: 10px;">Email</th>
                        <th style="padding: 10px; text-align: center;">Ações / Redefinir Senha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res_usu = mysqli_query($conn, "SELECT id, nome, email FROM usuarios ORDER BY id DESC");
                    while ($u = mysqli_fetch_assoc($res_usu)) {
                        echo "<tr style='border-bottom: 1px solid #eee;'>";
                        echo "<td style='padding: 10px;'>" . $u['id'] . "</td>";
                        echo "<td style='padding: 10px;'>" . htmlspecialchars($u['nome']) . "</td>";
                        echo "<td style='padding: 10px;'>" . htmlspecialchars($u['email']) . "</td>";
                        echo "<td style='padding: 10px; text-align: center;'>";
                        
                    
                        echo "<form action='admin.php' method='POST' style='display:inline-block; margin-right: 10px;'>";
                        echo "<input type='hidden' name='usuario_id' value='" . $u['id'] . "'>";
                        echo "<input type='text' name='nova_senha' placeholder='Nova senha' required style='padding: 4px; font-size: 12px; width: 90px; border: 1px solid #ccc; border-radius: 3px;'> ";
                        echo "<button type='submit' name='alterar_senha' style='background: #f0ad4e; color: white; border: none; padding: 4px 8px; font-size: 12px; border-radius: 3px; cursor: pointer;'>Alterar</button>";
                        echo "</form>";

                
                        if ($u['id'] != $usuario_logado_id) {
                            echo "<a href='admin.php?deletar_usuario=" . $u['id'] . "' onclick='return confirm(\"Tem certeza que deseja apagar esta conta por completo?\");' style='color: #d9534f; text-decoration: none; font-weight: bold; font-size: 12px;'>🗑️ Excluir</a>";
                        } else {
                            echo "<span style='color: #888; font-size: 11px;'>(Você)</span>";
                        }

                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        
        <section style="margin-top: 40px;">
            <h3 style="color: #333;">Moderação de Publicações da Comunidade</h3>
            <p style="font-size: 14px; color: #666;">Como administradora, pode visualizar todos os relatos postados e remover qualquer conteúdo que viole as diretrizes da plataforma.</p>
            
            <div style="margin-top: 15px;">
                <?php
                $res_posts = mysqli_query($conn, "SELECT p.*, u.nome FROM publicacoes p JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.id DESC");
                
                if (mysqli_num_rows($res_posts) > 0) {
                    while ($post = mysqli_fetch_assoc($res_posts)) {
                        $pub_id = $post['id'];
                        echo "<div style='background: #fdfdfd; border: 1px solid #ddd; padding: 12px; border-radius: 6px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;'>";
                        
                        echo "<div>";
                        echo "<b style='color: #8a4baf;'>" . htmlspecialchars($post['nome']) . "</b>: ";
                        echo "<span>" . htmlspecialchars(substr($post['conteudo'], 0, 90)) . (strlen($post['conteudo']) > 90 ? '...' : '') . "</span>";
                        echo "<br><small style='color: #777;'>Data: " . $post['data_criacao'] . "</small>";
                        echo "</div>";

                        echo "<div>";
                        echo "<a href='admin.php?deletar_pub=$pub_id' onclick='return confirm(\"Deseja moderar e apagar esta publicação?\");' style='background: #d9534f; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold;'>Excluir Post</a>";
                        echo "</div>";

                        echo "</div>";
                    }
                } else {
                    echo "<p style='color: #666; font-size: 14px;'>Nenhuma publicação registada.</p>";
                }
                ?>
            </div>
        </section>

        <div style="margin-top: 30px; text-align: center;">
            <a href="rede.php" style="background: #8a4baf; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">Voltar para a Rede Social</a>
        </div>

    </main>

</body>
</html>
<?php 
require_once 'footer.php'; 
?>