<?php
$host = getenv("DB_HOST") ?: "db";
$user = getenv("DB_USER") ?: "amino";
$pass = getenv("DB_PASSWORD") ?: "";
$db   = getenv("DB_NAME") ?: "amino2";
$port = (int) (getenv("DB_PORT") ?: 3306);

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Erro de conexão com o banco");
}
?>