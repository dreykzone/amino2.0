<?php
session_start();
header('Content-Type: application/json');

// verifica login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'msg' => 'Não logado']);
    exit;
}

$conn = new mysqli("localhost", "root", "", "amino2");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'msg' => 'Erro de conexão']);
    exit;
}

$id_post = intval($_POST['id_post']);
$id_usuario = $_SESSION['user_id'];

// verifica se já curtiu
$sqlCheck = "SELECT id FROM curtidas WHERE id_usuario=? AND id_post=?";
$stmt = $conn->prepare($sqlCheck);
$stmt->bind_param("ii", $id_usuario, $id_post);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    // remove curtida
    $stmtDel = $conn->prepare("DELETE FROM curtidas WHERE id_usuario=? AND id_post=?");
    $stmtDel->bind_param("ii", $id_usuario, $id_post);
    $stmtDel->execute();
    $usuarioCurtiu = false;
} else {
    // adiciona curtida
    $stmtIns = $conn->prepare("INSERT INTO curtidas (id_usuario, id_post) VALUES (?, ?)");
    $stmtIns->bind_param("ii", $id_usuario, $id_post);
    $stmtIns->execute();
    $usuarioCurtiu = true;
}

// retorna total e status atualizado
$sqlTotal = "SELECT COUNT(*) as total FROM curtidas WHERE id_post=?";
$stmtTot = $conn->prepare($sqlTotal);
$stmtTot->bind_param("i", $id_post);
$stmtTot->execute();
$data = $stmtTot->get_result()->fetch_assoc();

echo json_encode([
    'success' => true,
    'total' => $data['total'],
    'usuarioCurtiu' => $usuarioCurtiu
]);

