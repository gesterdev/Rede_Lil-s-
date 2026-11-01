<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'header.php'; 
?>

<html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Rede Lilás</title>
    <link rel="stylesheet" href="index.css">
</head>

<body>

    <main class="conteudo">

        <h1>Rede Lilás</h1>

        <p class="mensagem">
            A Rede Lilás é uma rede social exclusiva para mulheres, voltada ao acolhimento, apoio mútuo e à conscientização sobre a violência contra a mulher.
        </p>

        <div class="container-foto">
    <div class="fotos">
        <img src="foto1.jpg" alt="Rede Lilás">
        <img src="foto2.jpg" alt="Rede Lilás">
        <img src="foto3.jpg" alt="Rede Lilás">
        <img src="foto4.jpg" alt="Rede Lilás">
    </div>
</div>

        <section class="apresentacao">
            <h2>Sobre a Rede Lilás</h2>
            <p>
                Aqui, as usuárias poderão compartilhar publicações,
                interagir com outras mulheres e encontrar informações
                importantes sobre violência contra a mulher.
            </p>
        </section>

    </main>
</body>
</html>

<?php 
require_once 'footer.php'; 
?>