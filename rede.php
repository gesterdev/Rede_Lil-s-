<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once  'db.php';
require_once 'auth.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

if (isset($_POST['publicar'])) {
    $conteudo = trim($_POST['conteudo']);
    $arquivo_path = "";

    if (isset($_FILES['midia']) && $_FILES['midia']['error'] === UPLOAD_ERR_OK) {
        $pasta = "uploads/";
        if (!is_dir($pasta)) {
            mkdir($pasta, 0755, true);
        }
        
        $nome_original = basename($_FILES['midia']['name']);
        $destino = $pasta . time() . "_" . $nome_original;
        
        if (move_uploaded_file($_FILES['midia']['tmp_name'], $destino)) {
            $arquivo_path = $destino;
        }
    }

    if (!empty($conteudo) || !empty($arquivo_path)) {
        $sql = "INSERT INTO publicacoes (usuario_id, conteudo, arquivo_path) VALUES ('$usuario_id', '$conteudo', '$arquivo_path')";
        mysqli_query($conn, $sql);
        
        header("Location: rede.php");
        exit();
    }
}

if (isset($_GET['curtir'])) {
    $pub_id = intval($_GET['curtir']);
    $sql = "INSERT INTO curtidas (publicacao_id, usuario_id) VALUES ('$pub_id', '$usuario_id')";
    mysqli_query($conn, $sql);
    header("Location: rede.php");
    exit();
}

if (isset($_POST['comentar'])) {
    $pub_id = intval($_POST['publicacao_id']);
    $conteudo_com = trim($_POST['conteudo_comentario']);
    $com_arquivo = "";

    if (isset($_FILES['comentario_midia']) && $_FILES['comentario_midia']['error'] === UPLOAD_ERR_OK) {
        $pasta = "uploads/";
        if (!is_dir($pasta)) {
            mkdir($pasta, 0755, true);
        }
        $nome_original = basename($_FILES['comentario_midia']['name']);
        $destino = $pasta . time() . "_com_" . $nome_original;
        
        if (move_uploaded_file($_FILES['comentario_midia']['tmp_name'], $destino)) {
            $com_arquivo = $destino;
        }
    }

    if (!empty($conteudo_com) || !empty($com_arquivo)) {
        $sql = "INSERT INTO comentarios (publicacao_id, usuario_id, conteudo, arquivo_path) VALUES ('$pub_id', '$usuario_id', '$conteudo_com', '$com_arquivo')";
        mysqli_query($conn, $sql);
        
        header("Location: rede.php");
        exit();
    }
}

if (isset($_GET['deletar_pub'])) {
    $pub_id = intval($_GET['deletar_pub']);
    
    $verifica = mysqli_query($conn, "SELECT id, arquivo_path FROM publicacoes WHERE id = $pub_id AND usuario_id = $usuario_id");
    if ($verifica && mysqli_num_rows($verifica) > 0) {
        $dados_pub = mysqli_fetch_assoc($verifica);
        
        if (!empty($dados_pub['arquivo_path']) && file_exists($dados_pub['arquivo_path'])) {
            unlink($dados_pub['arquivo_path']);
        }
        
        mysqli_query($conn, "DELETE FROM curtidas WHERE publicacao_id = $pub_id");
        mysqli_query($conn, "DELETE FROM comentarios WHERE publicacao_id = $pub_id");
        mysqli_query($conn, "DELETE FROM publicacoes WHERE id = $pub_id");
    }
    header("Location: rede.php");
    exit();
}

if (isset($_GET['deletar_com'])) {
    $com_id = intval($_GET['deletar_com']);
    
    $verifica_com = mysqli_query($conn, "SELECT id, arquivo_path FROM comentarios WHERE id = $com_id AND usuario_id = $usuario_id");
    if ($verifica_com && mysqli_num_rows($verifica_com) > 0) {
        $dados_com = mysqli_fetch_assoc($verifica_com);
        
        if (!empty($dados_com['arquivo_path']) && file_exists($dados_com['arquivo_path'])) {
            unlink($dados_com['arquivo_path']);
        }
        
        mysqli_query($conn, "DELETE FROM comentarios WHERE id = $com_id");
    }
    header("Location: rede.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Comunidade - Rede Lilás</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

    <?php require_once 'header.php'; ?>

    <main class="conteudo" style="max-width: 650px; margin: 30px auto; padding: 20px;">
        
        <h2>Bem-vinda, <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'usuária'); ?>!</h2>
        
        <div style="background-color: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd;">
            <form action="rede.php" method="GET" style="display: flex; gap: 10px;">
                <input type="text" name="busca" placeholder="Pesquisar publicações ou relatos..." value="<?php echo htmlspecialchars($_GET['busca'] ?? ''); ?>" style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px;">
                <button type="submit" style="background-color: #8a4baf; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Buscar</button>
                <?php if (isset($_GET['busca']) && !empty($_GET['busca'])): ?>
                    <a href="rede.php" style="background-color: #ccc; color: #333; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; display: flex; align-items: center; justify-content: center;">Limpar</a>
                <?php endif; ?>
            </form>
        </div>

        <div style="background-color: #ffffff; padding: 15px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #ccc;">
            <h3 style="margin-top: 0; color: #8a4baf;">Partilhe algo com a rede</h3>
            
            <form action="rede.php" method="POST" enctype="multipart/form-data">
                <textarea 
                    name="conteudo" 
                    placeholder="Escreva a sua publicação, relato ou pensamento..." 
                    style="width: 100%; height: 90px; padding: 10px; border-radius: 5px; border: 1px solid #aaa; box-sizing: border-box;"
                ></textarea>
                
                <div style="margin: 10px 0; font-size: 14px; color: #555;">
                    <label style="color: #8a4baf; font-weight: bold; cursor: pointer;">📎 Anexar Foto ou Vídeo:</label>
                    <input type="file" name="midia" accept="image/*,video/*" style="display: block; margin-top: 5px;">
                </div>

                <button type="submit" name="publicar" style="background-color: #8a4baf; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold;">
                    Publicar
                </button>
            </form>
        </div>

        <h3 style="color: #333;">Linha do Tempo</h3>
        
        <?php
        $termo_busca = trim($_GET['busca'] ?? '');
        if (!empty($termo_busca)) {
            $termo_seguro = mysqli_real_escape_string($conn, $termo_busca);
            $sql = "SELECT p.*, u.nome FROM publicacoes p JOIN usuarios u ON p.usuarios_id = u.id WHERE p.conteudo LIKE '%$termo_seguro%' OR u.nome LIKE '%$termo_seguro%' ORDER BY p.id DESC";
        } else {
            $sql = "SELECT p.*, u.nome FROM publicacoes p JOIN usuarios u ON p.usuarios_id = u.id ORDER BY p.id DESC";
        }
        
        $resultado = mysqli_query($conn, $sql);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            while ($post = mysqli_fetch_assoc($resultado)) {
                $pub_id = $post['id'];
                $dono_post_id = $post['usuario_id'];

                echo "<div style='background-color: white; border: 1px solid #ddd; padding: 15px; border-radius: 8px; margin-bottom: 20px; position: relative;'>";
                
                echo "<div style='display: flex; justify-content: space-between; align-items: center;'>";
                echo "<strong style='color: #8a4baf; font-size: 16px;'>" . htmlspecialchars($post['nome']) . "</strong>";
                
                if ($dono_post_id == $usuario_id) {
                    echo "<a href='rede.php?deletar_pub=$pub_id' onclick='return confirm(\"Tem certeza que deseja apagar esta publicação?\");' style='color: #d9534f; font-size: 12px; text-decoration: none; font-weight: bold;'>🗑️ Excluir</a>";
                }
                echo "</div>";
                
                if (!empty($post['conteudo'])) {
                    echo "<p style='margin: 10px 0;'>" . nl2br(htmlspecialchars($post['conteudo'])) . "</p>";
                }

                if (!empty($post['arquivo_path'])) {
                    $ext = strtolower(pathinfo($post['arquivo_path'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        echo "<div style='margin: 10px 0;'><img src='" . htmlspecialchars($post['arquivo_path']) . "' style='max-width: 100%; max-height: 300px; border-radius: 4px;'></div>";
                    } elseif (in_array($ext, ['mp4', 'webm'])) {
                        echo "<div style='margin: 10px 0;'><video controls style='max-width: 100%; max-height: 300px;'><source src='" . htmlspecialchars($post['arquivo_path']) . "'></video></div>";
                    }
                }

                echo "<small style='color: #777; display: block; margin-top: 5px;'>" . date('d/m/Y H:i', strtotime($post['data_criacao'])) . "</small>";

                $sql_c = mysqli_query($conn, "SELECT COUNT(*) as total FROM curtidas WHERE publicacao_id = $pub_id");
                $tot_c = mysqli_fetch_assoc($sql_c)['total'];

                echo "<div style='margin-top: 10px;'>";
                echo "<a href='rede.php?curtir=$pub_id' style='color: #8a4baf; text-decoration: none; font-weight: bold;'>❤️ Curtir ($tot_c)</a>";
                echo "</div>";

                echo "<div style='margin-top: 15px; background: #fafafa; padding: 10px; border-radius: 5px; border: 1px solid #eee;'>";
                echo "<b style='font-size: 13px; color: #555;'>Comentários:</b><br>";

                $sql_com = mysqli_query($conn, "SELECT c.*, u.nome FROM comentarios c JOIN usuarios u ON c.usuario_id = u.id WHERE c.publicacao_id = $pub_id ORDER BY c.id ASC");
                if ($sql_com && mysqli_num_rows($sql_com) > 0) {
                    while ($com = mysqli_fetch_assoc($sql_com)) {
                        $com_id = $com['id'];
                        $dono_com_id = $com['usuario_id'];

                        echo "<div style='font-size: 13px; margin-top: 6px; border-bottom: 1px dashed #ddd; padding-bottom: 6px; display: flex; justify-content: space-between; align-items: start;'>";
                        
                        echo "<div>";
                        echo "<b style='color: #8a4baf;'>" . htmlspecialchars($com['nome']) . ":</b> " . htmlspecialchars($com['conteudo']);
                        
                        if (!empty($com['arquivo_path'])) {
                            echo "<br><img src='" . htmlspecialchars($com['arquivo_path']) . "' style='max-width: 80px; max-height: 80px; margin-top: 4px; border-radius: 3px;'>";
                        }
                        echo "</div>";

                        if ($dono_com_id == $usuario_id) {
                            echo "<a href='rede.php?deletar_com=$com_id' onclick='return confirm(\"Apagar este comentário?\");' style='color: #d9534f; font-size: 11px; text-decoration: none;'>❌</a>";
                        }

                        echo "</div>";
                    }
                } else {
                    echo "<p style='font-size: 12px; color: #777; margin: 5px 0;'>Nenhum comentário ainda.</p>";
                }

                echo "<form action='rede.php' method='POST' enctype='multipart/form-data' style='margin-top: 8px;'>";
                echo "<input type='hidden' name='publicacao_id' value='$pub_id'>";
                echo "<input type='text' name='conteudo_comentario' placeholder='Escreva um comentário...' style='width: 58%; padding: 5px; font-size: 12px; border: 1px solid #ccc; border-radius: 3px;'>";
                echo " <input type='file' name='comentario_midia' accept='image/*' style='font-size: 11px; width: 85px;'>";
                echo " <button type='submit' name='comentar' style='background: #8a4baf; color: white; border: none; padding: 5px 8px; font-size: 12px; border-radius: 3px; cursor: pointer;'>Enviar</button>";
                echo "</form>";

                echo "</div>"; 
                echo "</div>"; 
            }
        } else {
            echo "<p style='color: #666;'>Ainda não foram encontradas publicações.</p>";
        }
        ?>

    </main>

</body>
</html>

<?php 
require_once 'footer.php'; 
?>