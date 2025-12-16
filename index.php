<?php
session_start();

// Se existir sessão ativa, destrói
if (isset($_SESSION["user_id"])) {
    $_SESSION = [];
    session_destroy();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Amino 2.0</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f5f5f5;
            color: #333;
        }

        header {
            background: #5a2d82;
            color: #fff;
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 24px;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
            opacity: 0.9;
        }

        nav a:hover {
            opacity: 1;
        }

        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
        }

        main h2 {
            font-size: 32px;
            margin-bottom: 16px;
            color: #5a2d82;
        }

        main p {
            max-width: 600px;
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .badge {
            display: inline-block;
            background: #ddd;
            color: #555;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
        }

        footer {
            background: #222;
            color: #ccc;
            text-align: center;
            padding: 12px;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <header>
        <h1>Amino 2.0</h1>
        <nav>
            <a href="login.php">Login</a>
            <a href="cadastrar.php">Cadastro</a>
        </nav>
    </header>

    <main>
        <h2>Bem-vindo ao Amino 2.0</h2>
        <p>
            Amino 2.0 é a nova geração de comunidades online.
            Um espaço para criar, conversar, compartilhar ideias
            e se conectar com pessoas que curtem as mesmas coisas que você.
        </p>
        <span class="badge">Em desenvolvimento</span>
    </main>

    <footer>
        © 2025 Andrey. Todos os direitos reservados.
    </footer>

</body>

</html>