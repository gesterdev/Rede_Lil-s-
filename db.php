<?php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "rede_lilas";

$conn = mysqli_connect($servidor, $usuario, $senha, $banco);

if (!$conn) {
    die("Erro na conexão: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>