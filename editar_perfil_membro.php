<?php
require_once 'auth.php';

$conn = new mysqli("localhost", "root", "", "amino2");

$id_usuario = $_SESSION['user_id'];
$id_comunidade = intval($_POST['id_comunidade']);
$nickname = trim($_POST['nickname'] ?? '');
$bio = trim($_POST['bio'] ?? '');

$avatarPath = null;

if (!empty($_FILES['avatar']['name'])) {
    $dir = "uploads/avatars/";
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    $nome = uniqid() . "_" . basename($_FILES['avatar']['name']);
    $destino = $dir . $nome;

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destino)) {
        $avatarPath = $destino;
    }
}

$sql = "
UPDATE membros_comunidade 
SET nickname = ?, bio = ?, avatar = COALESCE(?, avatar)
WHERE id_usuario = ? AND id_comunidade = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sssii",
    $nickname,
    $bio,
    $avatarPath,
    $id_usuario,
    $id_comunidade
);

$stmt->execute();

header("Location: comunidade.php?id=" . $id_comunidade);
?>