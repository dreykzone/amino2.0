<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "amino2";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro de conexão com o banco");
}
?>