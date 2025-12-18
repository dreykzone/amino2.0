<?php
require_once 'auth.php';
require_once 'db.php';

$id_usuario = $_SESSION['user_id'];
$id_comunidade = intval($_POST['id_comunidade'] ?? 0);


$sql = "INSERT IGNORE INTO membros_comunidade (id_usuario, id_comunidade) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_usuario, $id_comunidade);
$stmt->execute();


header("Location: comunidade.php?id=$id_comunidade");
exit;
