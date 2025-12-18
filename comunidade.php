<?php
require_once 'auth.php';
require_once 'db.php';

$id = intval($_GET["id"] ?? 0);

/* =========================
   BUSCA DADOS DA COMUNIDADE
========================= */
$sql = "SELECT c.*, u.username 
        FROM comunidades c 
        JOIN users u ON u.id = c.id_criador 
        WHERE c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0)
    die("Comunidade não encontrada");

$comunidade = $result->fetch_assoc();

/* =========================
   VERIFICA SE É MEMBRO
========================= */
if ($_SESSION['user_id'] == $comunidade['id_criador']) {
    $isMembro = true;
} else {
    $sqlMembro = "SELECT 1 
                  FROM membros_comunidade 
                  WHERE id_usuario = ? AND id_comunidade = ?";
    $stmtMembro = $conn->prepare($sqlMembro);
    $stmtMembro->bind_param("ii", $_SESSION['user_id'], $comunidade['id']);
    $stmtMembro->execute();
    $resultMembro = $stmtMembro->get_result();
    $isMembro = $resultMembro->num_rows > 0;
}

$perfilMembro = null;

if ($isMembro) {
    $sqlPerfil = "
        SELECT nickname, bio, avatar
        FROM membros_comunidade
        WHERE id_usuario = ? AND id_comunidade = ?
        LIMIT 1
    ";
    $stmtPerfil = $conn->prepare($sqlPerfil);
    $stmtPerfil->bind_param("ii", $_SESSION['user_id'], $comunidade['id']);
    $stmtPerfil->execute();
    $perfilMembro = $stmtPerfil->get_result()->fetch_assoc();
}


/* =========================
   NOME EXIBIDO (NICK OU GLOBAL)
========================= */
$sqlUser = "
SELECT 
    COALESCE(NULLIF(mc.nickname, ''), u.username) AS nome_exibido
FROM users u
LEFT JOIN membros_comunidade mc
    ON mc.id_usuario = u.id
   AND mc.id_comunidade = ?
WHERE u.id = ?
LIMIT 1
";

$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("ii", $id, $_SESSION['user_id']);
$stmtUser->execute();
$userResult = $stmtUser->get_result();
$userData = $userResult->fetch_assoc();

$nomeExibido = $userData['nome_exibido'] ?? $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($comunidade["nome"]) ?> - Amino 2.0</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
    <link rel="shortcut icon" href="assets/favicon.ico" type="image/x-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Nunito', sans-serif;
        }

        body {
            min-height: 100vh;
            background: radial-gradient(circle at top,
                    #efe6f8 0%,
                    #f5f5f5 60%);
            color: #333;
            display: flex;
            flex-direction: column;
        }

        header {
            background: linear-gradient(135deg, #5a2d82, #6d3aa0);
            backdrop-filter: blur(10px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            color: #fff;
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 24px;
        }

        .user {
            font-weight: bold;
            opacity: 0.9;
        }

        main {
            flex: 1;
            padding: 40px 30px;
        }

        .box {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            max-width: 900px;
            margin: auto;
        }


        .box h2 {
            color: #5a2d82;
            margin-bottom: 10px;
        }

        .creator {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
        }


        .post {
            border: 1px solid #ccc;
            background: #fff;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .post h4 {
            color: #5a2d82;
            margin-bottom: 8px;
        }

        .post small {
            color: #666;
            font-size: 12px;
        }

        .fab {
            position: fixed;
            bottom: 25px;
            right: 25px;
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

        .fab-mini {
            width: 50px;
            height: 50px;
            background: #5a2d82;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            cursor: pointer;
            font-size: 18px;
        }


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
            z-index: 999;
        }

        .modal-content {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            width: 360px;
            max-width: 90%;
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
            resize: vertical;
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

        .btn-comentario {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #f2eef7;
            color: #5a2d82;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .btn-comentario:hover {
            background: #e3d9ef;
            transform: translateY(-1px);
        }

        .btn-comentario:active {
            transform: scale(0.97);
        }

        .btn-comentario span {
            font-size: 15px;
        }

        .comentario {
            background: #f8f6fb;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 10px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        }

        .comentario-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .comentario-user {
            font-weight: bold;
            color: #5a2d82;
            font-size: 14px;
        }

        .comentario-data {
            font-size: 11px;
            color: #999;
        }

        .comentario-texto {
            font-size: 14px;
            color: #333;
            line-height: 1.4;
            word-wrap: break-word;
        }

        footer {
            background: #222;
            color: #ccc;
            text-align: center;
            padding: 12px;
            font-size: 14px;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.6);
        }

        .community-header {
            position: relative;
            height: 220px;
            background-size: cover;
            background-position: center;
            border-radius: 12px;
            margin-bottom: 70px;
            /* espaço pra logo sair pra fora */
        }

        .community-logo {
            position: absolute;
            bottom: -45px;
            left: 30px;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            background: #fff;
            border: 4px solid #fff;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>

<body>

    <header>
        <h1>Amino 2.0</h1>
        <div class="user" <?php if ($isMembro): ?>onclick="abrirModalPerfil()" <?php endif; ?>
            style="cursor:pointer; display:flex; align-items:center; gap:10px;">

            <?php if ($isMembro && !empty($perfilMembro['avatar'])): ?>
                <img src="<?= htmlspecialchars($perfilMembro['avatar']) ?>" class="avatar">
            <?php endif; ?>

            @<?= htmlspecialchars($nomeExibido) ?>
        </div>

    </header>

    <main>
        <div class="box">
            <div class="community-header" style="background-image: url('<?= htmlspecialchars(
                $comunidade['background'] ?: 'assets/bg-default.jpg'
            ) ?>');">

                <?php if (!empty($comunidade["imagem"])): ?>
                    <img class="community-logo" src="<?= htmlspecialchars($comunidade["imagem"]) ?>"
                        alt="Logo da comunidade">
                <?php endif; ?>
            </div>


            <h2><?= htmlspecialchars($comunidade["nome"]) ?></h2>
            <p><?= htmlspecialchars($comunidade["descricao"]) ?></p>
            <p class="creator">Criado por @<?= htmlspecialchars($comunidade["username"]) ?></p>

            <?php if (!$isMembro): ?>
                <form action="entrar_comunidade.php" method="POST" style="margin:15px 0;">
                    <input type="hidden" name="id_comunidade" value="<?= $comunidade['id'] ?>" />
                    <button type="submit"
                        style="padding:10px 20px; background:#5a2d82; color:#fff; border:none; border-radius:6px; cursor:pointer;">Entrar
                        na Comunidade</button>
                </form>
            <?php else: ?>
                <p style="color:green; font-weight:bold; margin-top: 5px;">Você é membro desta comunidade</p>
            <?php endif; ?>

            <hr style="margin:20px 0;">
            <h3 style="margin-bottom: 15px;">Posts</h3>

            <?php
            $sqlPosts = "SELECT 
    p.*,
    COALESCE(mc.nickname, u.username) AS nome_exibido
FROM posts p
JOIN users u ON u.id = p.id_usuario
LEFT JOIN membros_comunidade mc 
    ON mc.id_usuario = p.id_usuario 
   AND mc.id_comunidade = p.id_comunidade
WHERE p.id_comunidade = ?
ORDER BY p.data_criacao DESC";

            $stmtPosts = $conn->prepare($sqlPosts);
            $stmtPosts->bind_param("i", $comunidade['id']);
            $stmtPosts->execute();
            $resultPosts = $stmtPosts->get_result();
            while ($post = $resultPosts->fetch_assoc()):
                // Dentro do while($post = $resultPosts->fetch_assoc()):
                $sqlCurtidas = "SELECT COUNT(*) as total, 
                       SUM(CASE WHEN id_usuario = ? THEN 1 ELSE 0 END) as curtiu 
                FROM curtidas 
                WHERE id_post = ?";
                $stmtCurt = $conn->prepare($sqlCurtidas);
                $stmtCurt->bind_param("ii", $_SESSION['user_id'], $post['id']);
                $stmtCurt->execute();
                $resultCurt = $stmtCurt->get_result()->fetch_assoc();
                $totalCurtidas = $resultCurt['total'];
                $usuarioCurtiu = $resultCurt['curtiu'] > 0;

                ?>
                <div class="post">
                    <h4><?= htmlspecialchars($post['titulo']) ?></h4>
                    <p><?= nl2br(htmlspecialchars($post['texto'])) ?></p>
                    <small>Publicado por @<?= htmlspecialchars($post['nome_exibido']) ?> em
                        <?= $post['data_criacao'] ?></small>
                    <div style="margin-top:10px; display: flex; gap: 10px;">
                        <button type="button" class="btn-comentario" onclick="abrirModalComentarios(<?= $post['id'] ?>)">
                            <span>💬</span> Comentários
                        </button>
                        <?php if ($isMembro): ?>
                            <button onclick="toggleCurtir(<?= $post['id'] ?>, this)"
                                style="background:none; border:none; cursor:pointer; font-size:16px;">
                                <?= $usuarioCurtiu ? '❤️' : '🤍' ?> <span
                                    id="count-<?= $post['id'] ?>"><?= $totalCurtidas ?></span>
                            </button>
                        <?php else: ?>
                            <button style="background:none; border:none; cursor:pointer; font-size:16px;">
                                <?= $usuarioCurtiu ? '❤️' : '🤍' ?> <span
                                    id="count-<?= $post['id'] ?>"><?= $totalCurtidas ?></span>
                            </button>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <footer>
        © 2025 Andrey. Todos os direitos reservados.
    </footer>

    <?php if ($isMembro): ?>
        <div class="fab" onclick="abrirMenuPosts()">+</div>
        <div id="menuPosts" style="display:none; position:fixed; bottom:100px; right:30px;">
            <div class="fab-mini" onclick="abrirModalBlog()">Blog</div>
        </div>

        <div class="modal" id="modalBlog">
            <div class="modal-content">
                <h2>Criar Post</h2>
                <form action="criar_post.php" method="POST">
                    <input type="hidden" name="id_comunidade" value="<?= $comunidade['id'] ?>" />
                    <input type="text" name="titulo" placeholder="Título" required />
                    <textarea name="texto" placeholder="Conteúdo do post" required></textarea>
                    <button type="submit">Publicar</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="modal" id="modalComentarios">
        <div class="modal-content">
            <h2>Comentários</h2>
            <div id="listaComentarios" style="max-height:300px; overflow-y:auto; margin-bottom:12px;">
                <!-- Comentários serão carregados aqui via PHP -->
            </div>
            <?php if ($isMembro): ?>
                <form id="formComentario" method="POST" action="criar_comentario.php">
                    <input type="hidden" name="id_post" id="comentarioPostId">
                    <input type="hidden" name="id_comunidade" value="<?= $comunidade['id'] ?>">
                    <textarea name="comentario" placeholder="Escreva seu comentário..." required></textarea>
                    <button type="submit">Enviar</button>
                </form>
            <?php else: ?>
                <p style="color:#999; font-style:italic;">Somente membros podem comentar.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($isMembro): ?>
        <div class="modal" id="modalPerfil">
            <div class="modal-content">
                <h2>Editar perfil na comunidade</h2>

                <form action="editar_perfil_membro.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_comunidade" value="<?= $comunidade['id'] ?>">

                    <input type="text" name="nickname" placeholder="Nickname"
                        value="<?= htmlspecialchars($perfilMembro['nickname'] ?? '') ?>">

                    <textarea name="bio"
                        placeholder="Sua bio"><?= htmlspecialchars($perfilMembro['bio'] ?? '') ?></textarea>

                    <input type="file" name="avatar" accept="image/*">

                    <button type="submit">Salvar</button>
                </form>
            </div>
        </div>
    <?php endif; ?>



    <script>
        function abrirMenuPosts() {
            const menu = document.getElementById('menuPosts');
            menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
            menu.style.flexDirection = 'column';
        }
        function abrirModalBlog() {
            document.getElementById('modalBlog').style.display = 'flex';
        }

        function abrirModalComentarios(postId) {
            document.getElementById('modalComentarios').style.display = 'flex';
            document.getElementById('comentarioPostId').value = postId;

            fetch(
                'listar_comentarios.php?id_post=' + postId +
                '&id_comunidade=<?= $comunidade['id'] ?>'
            )
                .then(res => res.text())
                .then(html => {
                    document.getElementById('listaComentarios').innerHTML = html;
                });
        }


        function toggleCurtir(postId, btn) {
            fetch('curtir_post.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id_post=' + postId
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const span = document.getElementById('count-' + postId);
                        if (span) {
                            span.innerText = data.total;
                        }
                        // Atualiza coração do botão
                        btn.innerHTML = (data.usuarioCurtiu ? '❤️' : '🤍') + ' ' + data.total;
                    }
                });
        }



        window.onclick = function (e) {
            const modalBlog = document.getElementById('modalBlog');
            const modalComentarios = document.getElementById('modalComentarios');
            const modalPerfil = document.getElementById('modalPerfil');

            if (e.target === modalBlog) {
                modalBlog.style.display = 'none';
            }
            if (e.target === modalComentarios) {
                modalComentarios.style.display = 'none';
            }
            if (e.target === modalPerfil) {
                modalPerfil.style.display = 'none';
            }
        };

        function abrirModalPerfil() {
            document.getElementById('modalPerfil').style.display = 'flex';
        }

    </script>

</body>

</html>