<?php
session_start();
$conn = new mysqli("localhost", "root", "", "amino2");

if ($conn->connect_error) {
    header("Location: login.php?erro=1");
    exit;
}

$email = $_POST["email"] ?? "";
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

$_SESSION["user_id"] = $user["id"];
$_SESSION["username"] = $user["username"];

header("Location: secao_comunidades.php");
exit;
