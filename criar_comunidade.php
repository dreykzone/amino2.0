<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "amino2");
if ($conn->connect_error) {
    die("Erro conexão");
}

$nome = trim($_POST["nome"] ?? "");
$descricao = trim($_POST["descricao"] ?? "");
$idCriador = $_SESSION["user_id"];
$imagemPath = null;

/* =========================
   UPLOAD IMAGEM
========================= */
if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] === 0) {
    $ext = pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION);
    $nomeArquivo = uniqid("comunidade_") . "." . $ext;

    if (!is_dir("uploads")) {
        mkdir("uploads", 0777, true);
    }

    $imagemPath = "uploads/" . $nomeArquivo;
    move_uploaded_file($_FILES["imagem"]["tmp_name"], $imagemPath);
}

/* =========================
   CRIA COMUNIDADE
========================= */
$sql = "INSERT INTO comunidades (id_criador, nome, descricao, imagem)
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $idCriador, $nome, $descricao, $imagemPath);
$stmt->execute();

/* 🔑 ID DA COMUNIDADE CRIADA */
$idComunidade = $conn->insert_id;

/* =========================
   CRIADOR VIRA MEMBRO
========================= */
$sqlMembro = "
INSERT INTO membros_comunidade 
(id_usuario, id_comunidade, data_entrada)
VALUES (?, ?, NOW())
";

$stmtMembro = $conn->prepare($sqlMembro);
$stmtMembro->bind_param("ii", $idCriador, $idComunidade);
$stmtMembro->execute();

/* =========================
   REDIRECT
========================= */
header("Location: comunidade.php?id=" . $idComunidade);
exit;
?>
