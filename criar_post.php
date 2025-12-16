<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "amino2");
if ($conn->connect_error) die("Erro conexão");

$id_usuario = $_SESSION['user_id'];
$id_comunidade = intval($_POST['id_comunidade'] ?? 0);
$titulo = $_POST['titulo'] ?? '';
$texto = $_POST['texto'] ?? '';

$sql = "INSERT INTO posts (id_usuario, id_comunidade, titulo, texto) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $id_usuario, $id_comunidade, $titulo, $texto);
$stmt->execute();

header("Location: comunidade.php?id=$id_comunidade");
exit;
