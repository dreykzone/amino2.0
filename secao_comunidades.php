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

$sql = "SELECT c.*, u.username 
        FROM comunidades c
        JOIN users u ON u.id = c.id_criador
        ORDER BY c.id DESC";

$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Amino 2.0 - Comunidades</title>
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

        /* HEADER / NAVBAR */
        header {
            background: #5a2d82;
            color: #fff;
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        header h1 {
            font-size: 24px;
        }

        .search {
            flex: 1;
            margin: 0 30px;
        }

        .search input {
            width: 100%;
            padding: 10px 14px;
            border-radius: 20px;
            border: none;
            outline: none;
        }

        .user {
            font-weight: bold;
            opacity: 0.9;
        }

        /* MAIN */
        main {
            flex: 1;
            padding: 40px 30px;
        }

        .comunidades {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 24px;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
        }

        .card h3 {
            color: #5a2d82;
            margin-bottom: 8px;
        }

        .card p {
            font-size: 14px;
            line-height: 1.5;
            color: #555;
        }

        /* BOTÃO FLUTUANTE */
        .fab {
            position: fixed;
            right: 25px;
            bottom: 25px;
            width: 60px;
            height: 60px;
            background: #5a2d82;
            color: #fff;
            border-radius: 50%;
            font-size: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.25);
        }

        /* MODAL */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            width: 360px;
        }

        .modal-content h2 {
            margin-bottom: 14px;
            color: #5a2d82;
        }

        .modal-content input,
        .modal-content textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .modal-content button {
            width: 100%;
            padding: 10px;
            background: #5a2d82;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        /* FOOTER */
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
        <div class="search">
            <input type="text" placeholder="Pesquisar comunidades..." />
        </div>
        <div class="user">
            <?php echo htmlspecialchars($_SESSION['username']); ?>
        </div>
    </header>

    <main>
        <div class="comunidades">
            <?php while ($c = $result->fetch_assoc()): ?>
                <div class="card" onclick="window.location='comunidade.php?id=<?= $c['id'] ?>'">
                    <?php if ($c["imagem"]): ?>
                        <img src="<?= htmlspecialchars($c["imagem"]) ?>"
                            style="width:100%; border-radius:10px; margin-bottom:10px;">
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($c["nome"]) ?></h3>
                    <p><?= htmlspecialchars($c["descricao"]) ?></p>
                    <small>Criado por @<?= htmlspecialchars($c["username"]) ?></small>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <div class="fab" onclick="abrirModal()">+</div>

    <div class="modal" id="modal">
        <div class="modal-content">
            <h2>Criar Comunidade</h2>
            <form action="criar_comunidade.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="nome" placeholder="Nome da comunidade" required />
                <textarea name="descricao" placeholder="Descrição" required></textarea>
                <input type="file" name="imagem" accept="image/*" />
                <button type="submit">Criar</button>
            </form>

        </div>
    </div>

    <footer>
        © 2025 Andrey. Todos os direitos reservados.
    </footer>

    <script>
        function abrirModal() {
            document.getElementById('modal').style.display = 'flex';
        }

        window.onclick = function (e) {
            const modal = document.getElementById('modal');
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        };
    </script>

</body>

</html>