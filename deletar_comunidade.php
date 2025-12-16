<?php
session_start();
if (!isset($_SESSION['user_id'])) header("Location: login.php");

$conn = new mysqli("localhost","root","","amino2");
if ($conn->connect_error) die("Erro de conexão");

$id = intval($_POST['id'] ?? 0);

// verifica se o usuário é o criador
$sql = "SELECT id_criador FROM comunidades WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows === 0) die("Comunidade não encontrada");

$comunidade = $result->fetch_assoc();
if($comunidade['id_criador'] != $_SESSION['user_id']) die("Ação não permitida");

// deleta posts da comunidade
$conn->query("DELETE FROM posts WHERE id_comunidade = $id");
// deleta membros
$conn->query("DELETE FROM membros_comunidade WHERE id_comunidade = $id");
// deleta a comunidade
$stmtDel = $conn->prepare("DELETE FROM comunidades WHERE id = ?");
$stmtDel->bind_param("i",$id);
$stmtDel->execute();

header("Location: secao_comunidades.php");
exit;
