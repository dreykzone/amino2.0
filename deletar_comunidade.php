<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost","root","","amino2");
if ($conn->connect_error) die("Erro de conexão");

$id = intval($_POST['id'] ?? 0);

// Verifica se a comunidade existe e se o usuário é o criador
$sql = "SELECT id_criador FROM comunidades WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) die("Comunidade não encontrada");

$comunidade = $result->fetch_assoc();
if ($comunidade['id_criador'] != $_SESSION['user_id']) die("Ação não permitida");

// Deleta comentários de todos os posts da comunidade
$conn->query("DELETE FROM comentarios WHERE id_post IN (SELECT id FROM posts WHERE id_comunidade = $id)");

// Deleta posts da comunidade
$conn->query("DELETE FROM posts WHERE id_comunidade = $id");

// Deleta membros da comunidade
$conn->query("DELETE FROM membros_comunidade WHERE id_comunidade = $id");

// Deleta a própria comunidade
$stmtDel = $conn->prepare("DELETE FROM comunidades WHERE id = ?");
$stmtDel->bind_param("i", $id);
$stmtDel->execute();

header("Location: secao_comunidades.php");
exit;
?>
