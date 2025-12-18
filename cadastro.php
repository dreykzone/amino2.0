<?php
require_once 'db.php';
header("Content-Type: application/json");

$username = $_POST["username"] ?? "";
$email = $_POST["email"] ?? "";
$password = $_POST["password"] ?? "";
$confirm_password = $_POST["confirm_password"] ?? "";

if ($password !== $confirm_password || !$username || !$email) {
    echo json_encode(["success" => false]);
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "SELECT id FROM users WHERE username = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header("Location: login.php?cadastro=falha");
exit;

}

$sql = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $email, $password_hash);
$stmt->execute();

header("Location: login.php?cadastro=sucesso");
exit;

