<?php
session_start();


if (isset($_SESSION["user_id"])) {
  $_SESSION = [];
  session_destroy();
}

$cadastroSucesso = isset($_GET["cadastro"]) && $_GET["cadastro"] === "sucesso";
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Amino 2.0 | Login</title>
  <link rel="shortcut icon" href="assets/favicon.ico" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
    rel="stylesheet">
  <style>
    body {
      font-family: 'Nunito', sans-serif;
      background: radial-gradient(circle at top,
          #efe6f8 0%,
          #f5f5f5 60%);
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      overflow: hidden;
    }

    .container {
      background: #fff;
      padding: 32px;
      width: 100%;
      max-width: 380px;
      border-radius: 8px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    h1 {
      text-align: center;
      color: #5a2d82;
      margin-bottom: 24px;
    }

    input {
      width: 100%;
      padding: 12px;
      margin-bottom: 16px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
      box-sizing: border-box;
    }

    button {
      width: 100%;
      padding: 12px;
      background: #5a2d82;
      color: #fff;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      cursor: pointer;
    }

    p {
      text-align: center;
      margin-top: 16px;
      font-size: 14px;
    }

    a {
      color: #5a2d82;
      text-decoration: none;
      font-weight: bold;
    }
  </style>
</head>

<body>

  <div class="container">
    <h1>Login</h1>

    <form action="valida_login.php" method="POST">
      <input name="email" type="email" placeholder="Email" required />
      <input name="password" type="password" placeholder="Senha" required />
      <button type="submit">Entrar</button>
    </form>


    <p>
      Não tem conta?
      <a href="cadastrar.php">Cadastre-se</a>
    </p>
  </div>

  <?php if ($cadastroSucesso): ?>
    <div id="modal" style="
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.6);
    display:flex;
    align-items:center;
    justify-content:center;
">
      <div style="
      background:#fff;
      padding:30px;
      border-radius:8px;
      text-align:center;
      width:300px;
  ">
        <h2>🎉 Conta criada!</h2>
        <p>Agora é só fazer login.</p>
        <button onclick="fecharModal()">OK</button>
      </div>
    </div>
  <?php endif; ?>

  <script>
    function fecharModal() {
      document.getElementById("modal").style.display = "none";
    }
  </script>


</body>

</html>