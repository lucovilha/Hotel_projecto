<?php
$host = 'localhost';
$utilizador = 'root';
$password = '';
$base_dados = 'hotel_db';

$conn = mysqli_connect($host, $utilizador, $password, $base_dados);

if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
?>
