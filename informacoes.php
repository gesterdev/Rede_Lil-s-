<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'header.php';
?>
<link rel="stylesheet" href="index.css">

    <main class="conteudo" style="padding: 20px; max-width: 800px; margin: 0 auto; color: #e0e0e0;">
        
        <h2 style="color: #1a1a1a; margin-bottom: 10px; border-bottom: 2px solid #333; padding-bottom: 10px;">Canais de Apoio e Informações</h2>
        <p style="color: #1a1a1a; margin-bottom: 25px; font-size: 16px;">
            Se você ou alguém que você conhece está passando por uma situação de violência, busque ajuda. Você não está sozinha!
        </p>

        <div style="background-color: #1a1a1a; border: 1px solid #333; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
            <h3 style="color: #ffffff; margin-top: 0; margin-bottom: 15px;">Canais Oficiais de Denúncia</h3>
            <ul style="line-height: 1.8; color: #cccccc; padding-left: 20px;">
                <li><strong style="color: #fff;">Ligue 180:</strong> Central de Atendimento à Mulher (Gratuito, confidencial e disponível 24h).</li>
                <li><strong style="color: #fff;">Ligue 190:</strong> Polícia Militar (Para situações de emergência imediata).</li>
            </ul>
        </div>

        <div style="background-color: #1a1a1a; border: 1px solid #333; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
            <h3 style="color: #ffffff; margin-top: 0; margin-bottom: 15px;">Tipos de Violência contra a Mulher (Lei Maria da Penha)</h3>
            <p style="color: #b0b0b0; margin-bottom: 15px;">A violência de género manifesta-se de diversas formas:</p>
            <ul style="line-height: 1.8; color: #cccccc; padding-left: 20px;">
                <li><strong style="color: #fff;">Física:</strong> Qualquer conduta que ofenda a integridade ou saúde corporal.</li>
                <li><strong style="color: #fff;">Psicológica:</strong> Ações que causem dano emocional, diminuição da autoestima ou controle de ações.</li>
                <li><strong style="color: #fff;">Moral:</strong> Condutas que configurem calúnia, difamação ou injúria.</li>
                <li><strong style="color: #fff;">Patrimonial:</strong> Retenção, subtração ou destruição de seus objetos, bens ou documentos.</li>
                <li><strong style="color: #fff;">Sexual:</strong> Ações que a forcem a presenciar, a manter ou a participar de relação sexual não desejada.</li>
            </ul>
        </div>
    </main>
</body>
</html>

<?php 
require_once 'footer.php'; 
?>