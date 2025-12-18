<?php
require_once 'db.php';
session_start();

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

$sql = "SELECT id, username, password_hash FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: login.php?erro=1");
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user["password_hash"])) {
    header("Location: login.php?erro=1");
    exit;
}

/* 🔐 REGENERA A SESSÃO AQUI */
session_regenerate_id(true);

/* SETA A SESSÃO */
$_SESSION["user_id"] = $user["id"];
$_SESSION["username"] = $user["username"];
$_SESSION["login_time"] = time(); // opcional, mas útil

header("Location: secao_comunidades.php");
exit;
?>