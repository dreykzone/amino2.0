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

$nome = $_POST["nome"] ?? "";
$descricao = $_POST["descricao"] ?? "";
$idCriador = $_SESSION["user_id"];
$imagemPath = null;


if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] === 0) {
    $ext = pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION);
    $nomeArquivo = uniqid("comunidade_") . "." . $ext;

    if (!is_dir("uploads")) {
        mkdir("uploads", 0777, true);
    }

    $imagemPath = "uploads/" . $nomeArquivo;
    move_uploaded_file($_FILES["imagem"]["tmp_name"], $imagemPath);
}

$sql = "INSERT INTO comunidades (id_criador, nome, descricao, imagem)
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $idCriador, $nome, $descricao, $imagemPath);
$stmt->execute();

header("Location: secao_comunidades.php");
exit;
