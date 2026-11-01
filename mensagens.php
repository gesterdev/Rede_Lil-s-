<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';
require_once 'auth.php';

$usuario_id = $_SESSION['usuario_id'];
$destinatario_id = isset($_GET['para']) ? intval($_GET['para']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_msg'])) {
    $dest_id = intval($_POST['destinatario_id']);
    $conteudo = trim($_POST['conteudo']);

    if (!empty($conteudo) && $dest_id > 0) {
        $stmt = $conn->prepare("INSERT INTO mensagens (remetente_id, destinatario_id, conteudo) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $usuario_id, $dest_id, $conteudo);
        $stmt->execute();
        $stmt->close();
        
        header("Location: mensagens.php?para=" . $dest_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Mensagens - Rede Lilás</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .chat-container {
            display: flex;
            max-width: 900px;
            height: 600px;
            margin: 30px auto;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .sidebar-conversas {
            width: 320px;
            border-right: 1px solid #eee;
            background: #fafafa;
            overflow-y: auto;
        }
        .sidebar-header {
            padding: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #8a4baf;
            border-bottom: 1px solid #eee;
        }
        .contato-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
        }
        .contato-item:hover, .contato-item.ativo {
            background: #f2e6fa;
        }
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fff;
        }
        .chat-header {
            padding: 15px 20px;
            background: #fdfcff;
            border-bottom: 1px solid #eee;
            font-weight: bold;
            color: #8a4baf;
            font-size: 16px;
        }
        .chat-mensagens {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: #f9f9fb;
        }
        .balao {
            max-width: 65%;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            line-height: 1.4;
            position: relative;
            word-wrap: break-word;
        }
        .balao.enviada {
            background: #8a4baf;
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 2px;
        }
        .balao.recebida {
            background: #e9e5f5;
            color: #333;
            align-self: flex-start;
            border-bottom-left-radius: 2px;
        }
        .balao small {
            display: block;
            font-size: 10px;
            margin-top: 4px;
            text-align: right;
            opacity: 0.8;
        }
        .chat-input-area {
            padding: 15px;
            background: #fff;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }
        .chat-input-area textarea {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            resize: none;
            height: 40px;
            font-family: inherit;
        }
        .chat-input-area button {
            background: #8a4baf;
            color: white;
            border: none;
            padding: 0 20px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }
        .sem-chat {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>

    <?php require_once 'header.php'; ?>

    <div class="chat-container">
        <div class="sidebar-conversas">
            <div class="sidebar-header">Direct / Mensagens</div>
            <?php
            $sql_usuarios = "SELECT id, nome, email FROM usuarios WHERE id != $usuario_id ORDER BY nome ASC";
            $res_usuarios = mysqli_query($conn, $sql_usuarios);

            if ($res_usuarios && mysqli_num_rows($res_usuarios) > 0) {
                while ($u = mysqli_fetch_assoc($res_usuarios)) {
                    $classe_ativo = ($destinatario_id == $u['id']) ? 'ativo' : '';
                    echo "<a href='mensagens.php?para=" . $u['id'] . "' class='contato-item $classe_ativo'>";
                    echo "<div>";
                    echo "<div style='font-weight: bold;'>" . htmlspecialchars($u['nome']) . "</div>";
                    echo "<div style='font-size: 12px; color: #666;'>" . htmlspecialchars($u['email']) . "</div>";
                    echo "</div>";
                    echo "</a>";
                }
            } else {
                echo "<p style='padding: 20px; color: #666; font-size: 14px;'>Nenhuma outra usuária cadastrada ainda.</p>";
            }
            ?>
        </div>

        <div class="chat-main">
            <?php if ($destinatario_id > 0): ?>
                <?php
                $sql_dest = "SELECT nome FROM usuarios WHERE id = $destinatario_id";
                $res_dest = mysqli_query($conn, $sql_dest);
                $nome_destinatario = ($res_dest && mysqli_num_rows($res_dest) > 0) ? mysqli_fetch_assoc($res_dest)['nome'] : "Usuária";
                ?>

                <div class="chat-header">
                    Conversando com: <?php echo htmlspecialchars($nome_destinatario); ?>
                </div>

                <div class="chat-mensagens" id="boxMensagens">
                    <?php
                    $sql_chat = "SELECT * FROM mensagens 
                                 WHERE (remetente_id = $usuario_id AND destinatario_id = $destinatario_id) 
                                    OR (remetente_id = $destinatario_id AND destinatario_id = $usuario_id) 
                                 ORDER BY data_envio ASC";
                    $res_chat = mysqli_query($conn, $sql_chat);

                    if ($res_chat && mysqli_num_rows($res_chat) > 0) {
                        while ($m = mysqli_fetch_assoc($res_chat)) {
                            $tipo_balao = ($m['remetente_id'] == $usuario_id) ? 'enviada' : 'recebida';
                            echo "<div class='balao $tipo_balao'>";
                            echo htmlspecialchars($m['conteudo']);
                            echo "<small>" . date('H:i', strtotime($m['data_envio'])) . "</small>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p style='text-align: center; color: #888; margin-top: auto; margin-bottom: auto;'>Ainda não há mensagens com " . htmlspecialchars($nome_destinatario) . ". Envie a primeira mensagem abaixo!</p>";
                    }
                    ?>
                </div>

                <form action="mensagens.php?para=<?php echo $destinatario_id; ?>" method="POST" class="chat-input-area">
                    <input type="hidden" name="destinatario_id" value="<?php echo $destinatario_id; ?>">
                    <textarea name="conteudo" required placeholder="Escreva uma mensagem..." rows="1" onkeydown="if(event.key === 'Enter' && !event.shiftKey){this.form.submit(); event.preventDefault();}"></textarea>
                    <button type="submit" name="enviar_msg">Enviar</button>
                </form>

                <script>
                    const box = document.getElementById('boxMensagens');
                    if(box) { box.scrollTop = box.scrollHeight; }
                </script>

            <?php else: ?>
                <div class="sem-chat">
                    <div>
                        <h3 style="color: #8a4baf; margin-bottom: 8px;">Suas Mensagens Privadas</h3>
                        <p style="font-size: 14px;">Selecione uma usuária na lista ao lado para iniciar ou continuar uma conversa.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
<?php require_once 'footer.php'; ?>