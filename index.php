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
    <link rel="shortcut icon" href="assets/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Nunito', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: radial-gradient(circle at top,
                    #efe6f8 0%,
                    #f5f5f5 60%);
            color: #333;
        }


        header {
            color: #fff;
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #5a2d82, #6d3aa0);
            backdrop-filter: blur(10px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
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

        .modal-aviso {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal-aviso-content {
            background: #fff;
            padding: 30px 28px;
            border-radius: 14px;
            max-width: 420px;
            width: 90%;
            text-align: center;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
            animation: aparecer 0.3s ease;
        }

        .modal-aviso-content h2 {
            color: #5a2d82;
            margin-bottom: 14px;
        }

        .modal-aviso-content p {
            font-size: 15px;
            color: #444;
            line-height: 1.5;
        }

        .modal-aviso-content button {
            margin-top: 20px;
            padding: 10px 22px;
            background: #5a2d82;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }

        .modal-aviso-content button:hover {
            opacity: 0.9;
        }

        @keyframes aparecer {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
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
        <img width="200" src="assets/logopng.png" alt="logo">
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

    <div id="modalAviso" class="modal-aviso">
        <div class="modal-aviso-content">
            <h2>⚠️ Ambiente de Testes</h2>
            <p>
                Este site está em <strong>fase de testes</strong>.
                <br><br>
                Não utilize dados reais ou sensíveis.
                <br>
                Caso utilize, a responsabilidade é inteiramente do usuário.
            </p>
            <button onclick="fecharAviso()">Entendi</button>
        </div>
    </div>

    <script>
        const MODAL_KEY = 'aviso_teste_aceito';

        function fecharAviso() {
            localStorage.setItem(MODAL_KEY, 'true');
            document.getElementById('modalAviso').style.display = 'none';
        }

        // Quando a página carregar
        window.addEventListener('DOMContentLoaded', () => {
            const aceito = localStorage.getItem(MODAL_KEY);

            if (aceito === 'true') {
                const modal = document.getElementById('modalAviso');
                if (modal) modal.style.display = 'none';
            }
        });
    </script>


</body>

</html>