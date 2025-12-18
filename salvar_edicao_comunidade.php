<?php
session_start();
if (!isset($_SESSION['user_id'])) header("Location: login.php");

$conn = new mysqli("localhost", "root", "", "amino2");
if ($conn->connect_error) die("Erro de conexão");

$id = intval($_POST['id'] ?? 0);
$nome = $_POST['nome'] ?? '';
$descricao = $_POST['descricao'] ?? '';

// pega comunidade
$sql = "SELECT * FROM comunidades WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) die("Comunidade não encontrada");

$comunidade = $result->fetch_assoc();
if ($comunidade['id_criador'] != $_SESSION['user_id']) die("Ação não permitida");

/* =========================
   LOGO / IMAGEM
========================= */
$imagemPath = $comunidade['imagem'];
if (!empty($_FILES['imagem']['name'])) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

    $nomeArquivo = uniqid("logo_") . "_" . basename($_FILES['imagem']['name']);
    $targetFile = $targetDir . $nomeArquivo;

    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $targetFile)) {
        $imagemPath = $targetFile;
    }
}

/* =========================
   BACKGROUND
========================= */
$backgroundPath = $comunidade['background'];
if (!empty($_FILES['background']['name'])) {
    $targetDir = "uploads/backgrounds/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

    $nomeArquivo = uniqid("bg_") . "_" . basename($_FILES['background']['name']);
    $targetFile = $targetDir . $nomeArquivo;

    if (move_uploaded_file($_FILES['background']['tmp_name'], $targetFile)) {
        $backgroundPath = $targetFile;
    }
}

/* =========================
   ATUALIZA COMUNIDADE
========================= */
$stmtUp = $conn->prepare(
    "UPDATE comunidades 
     SET nome = ?, descricao = ?, imagem = ?, background = ?
     WHERE id = ?"
);

$stmtUp->bind_param(
    "ssssi",
    $nome,
    $descricao,
    $imagemPath,
    $backgroundPath,
    $id
);

$stmtUp->execute();

header("Location: secao_comunidades.php");
exit;
?>