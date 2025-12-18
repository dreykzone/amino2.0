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


$sql = "SELECT 
    c.*, 
    u.username,
    COUNT(DISTINCT mc.id_usuario) AS total_membros
FROM comunidades c
JOIN users u ON u.id = c.id_criador
LEFT JOIN membros_comunidade mc 
    ON mc.id_comunidade = c.id
GROUP BY c.id
ORDER BY c.id DESC";


$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Amino 2.0 - Comunidades</title>
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

        /* HEADER / NAVBAR */
        header {
            background: linear-gradient(135deg, #5a2d82, #6d3aa0);
            backdrop-filter: blur(10px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
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
                <div class="card">
                    <?php if ($c["imagem"]): ?>
                        <img src="<?= htmlspecialchars($c["imagem"]) ?>"
                            style="width:100%; border-radius:10px; margin-bottom:10px;">
                    <?php endif; ?>

                    <h3 onclick="window.location='comunidade.php?id=<?= $c['id'] ?>'" style="cursor:pointer;">
                        <?= htmlspecialchars($c["nome"]) ?>
                    </h3>
                    <p><?= htmlspecialchars($c["descricao"]) ?></p>
                    <p style="margin-top:8px; font-size:13px; color:#777;">
                        👥 <?= $c['total_membros'] ?> membro<?= $c['total_membros'] > 1 ? 's' : '' ?>
                    </p>
                    <small>Criado por @<?= htmlspecialchars($c["username"]) ?></small>

                    <?php if ($_SESSION['user_id'] == $c['id_criador']): ?>
                        <div style="margin-top:10px; display:flex; gap:10px;">
                            <form action="editar_comunidade.php" method="GET" style="display:inline;"
                                onsubmit="event.stopPropagation();">
                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                <button type="button"
                                    onclick="event.stopPropagation(); abrirModalEditar('<?= $c['id'] ?>','<?= htmlspecialchars($c['nome'], ENT_QUOTES) ?>','<?= htmlspecialchars($c['descricao'], ENT_QUOTES) ?>')"
                                    style="padding:6px 12px; background:#f0ad4e; color:#fff; border:none; border-radius:6px; cursor:pointer;">
                                    Editar
                                </button>

                            </form>

                            <form action="deletar_comunidade.php" method="POST" style="display:inline;"
                                onsubmit="event.stopPropagation(); return confirm('Tem certeza que quer deletar?');">
                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                <button type="submit"
                                    style="padding:6px 12px; background:#d9534f; color:#fff; border:none; border-radius:6px; cursor:pointer;">Deletar</button>
                            </form>
                        </div>
                    <?php endif; ?>
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

    <div class="modal" id="modalEditar">
        <div class="modal-content">
            <h2>Editar Comunidade</h2>
            <form id="formEditar" action="salvar_edicao_comunidade.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editarId">
                <input type="text" name="nome" id="editarNome" placeholder="Nome da comunidade" required>
                <textarea name="descricao" id="editarDescricao" placeholder="Descrição" required></textarea>
                <input type="file" name="imagem" accept="image/*">
                <button type="submit">Salvar Alterações</button>
            </form>
        </div>
    </div>


    <script>
        function abrirModal() {
            document.getElementById('modal').style.display = 'flex';
        }

        function abrirModalEditar(id, nome, descricao) {
            document.getElementById('editarId').value = id;
            document.getElementById('editarNome').value = nome;
            document.getElementById('editarDescricao').value = descricao;
            document.getElementById('modalEditar').style.display = 'flex';
        }

        window.onclick = function (e) {
            const modalCriar = document.getElementById('modal');
            const modalEditar = document.getElementById('modalEditar');

            if (e.target === modalCriar) {
                modalCriar.style.display = 'none';
            }
            if (e.target === modalEditar) {
                modalEditar.style.display = 'none';
            }
        };

    </script>

</body>

</html>