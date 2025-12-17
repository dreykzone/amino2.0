<?php
session_start();
if (!isset($_SESSION['user_id'])) exit;

$idPost = intval($_POST['id_post'] ?? 0);
$idComu = intval($_POST['id_comunidade'] ?? 0);
$comentario = trim($_POST['comentario'] ?? '');

// Verifica se usuário é membro da comunidade
$conn = new mysqli("localhost","root","", "amino2");
$stmt = $conn->prepare("SELECT * FROM membros_comunidade WHERE id_usuario=? AND id_comunidade=?");
$stmt->bind_param("ii", $_SESSION['user_id'], $idComu);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("Ação não permitida");
}

// Insere comentário
$stmtIns = $conn->prepare("INSERT INTO comentarios (id_post, id_usuario, comentario, data_criacao) VALUES (?, ?, ?, NOW())");
$stmtIns->bind_param("iis", $idPost, $_SESSION['user_id'], $comentario);
$stmtIns->execute();

header("Location: comunidade.php?id=".$idComu);
exit;
?>
