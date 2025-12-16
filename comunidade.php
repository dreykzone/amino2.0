<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "amino2");
if ($conn->connect_error) die("Erro conexão");

$id = intval($_GET["id"] ?? 0);
$sql = "SELECT c.*, u.username FROM comunidades c JOIN users u ON u.id = c.id_criador WHERE c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) die("Comunidade não encontrada");
$comunidade = $result->fetch_assoc();


if ($_SESSION['user_id'] == $comunidade['id_criador']) {
    $isMembro = true; 
} else {
    $sqlMembro = "SELECT * FROM membros_comunidade WHERE id_usuario = ? AND id_comunidade = ?";
    $stmtMembro = $conn->prepare($sqlMembro);
    $stmtMembro->bind_param("ii", $_SESSION['user_id'], $comunidade['id']);
    $stmtMembro->execute();
    $resultMembro = $stmtMembro->get_result();
    $isMembro = $resultMembro->num_rows > 0;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?= htmlspecialchars($comunidade["nome"]) ?> - Amino 2.0</title>
<style>

* {margin:0;padding:0;box-sizing:border-box;font-family:Arial,Helvetica,sans-serif;}
body {min-height:100vh; background:#f5f5f5; color:#333; display:flex; flex-direction:column;}
header {background:#5a2d82;color:#fff;padding:16px 32px; display:flex; justify-content:space-between; align-items:center;}
header h1 {font-size:24px;}
.user {font-weight:bold;opacity:0.9;}
main {flex:1; padding:40px 30px;}
.box {background:#fff; border-radius:12px; padding:24px; box-shadow:0 4px 10px rgba(0,0,0,0.08); max-width:900px; margin:auto;}
.box img {width:100%; max-height:280px; object-fit:cover; border-radius:10px; margin-bottom:20px;}
.box h2 {color:#5a2d82; margin-bottom:10px;}
.creator {font-size:14px; color:#666; margin-top:10px;}


.post {background:#fff; padding:15px 20px; border-radius:10px; margin-bottom:15px; box-shadow:0 2px 6px rgba(0,0,0,0.08);}
.post h4 {color:#5a2d82; margin-bottom:8px;}
.post small {color:#666; font-size:12px;}

.fab {position:fixed; bottom:25px; right:25px; width:60px; height:60px; background:#5a2d82; color:#fff; border-radius:50%; font-size:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 6px 14px rgba(0,0,0,0.25);}
.fab-mini {width:50px; height:50px; background:#5a2d82; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:10px; cursor:pointer; font-size:18px;}


.modal {display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); align-items:center; justify-content:center; z-index:999;}
.modal-content {background:#fff; padding:24px; border-radius:12px; width:360px; max-width:90%;}
.modal-content h2 {margin-bottom:14px; color:#5a2d82;}
.modal-content input, .modal-content textarea {width:100%; padding:10px; margin-bottom:12px; border-radius:6px; border:1px solid #ccc; resize:vertical;}
.modal-content button {width:100%; padding:10px; background:#5a2d82; color:#fff; border:none; border-radius:6px; font-weight:bold; cursor:pointer;}
</style>
</head>
<body>

<header>
    <h1>Amino 2.0</h1>
    <div class="user"><?= htmlspecialchars($_SESSION["username"]) ?></div>
</header>

<main>
    <div class="box">
        <?php if (!empty($comunidade["imagem"])): ?>
            <img src="<?= htmlspecialchars($comunidade["imagem"]) ?>" alt="Imagem da comunidade">
        <?php endif; ?>

        <h2><?= htmlspecialchars($comunidade["nome"]) ?></h2>
        <p><?= htmlspecialchars($comunidade["descricao"]) ?></p>
        <p class="creator">Criado por @<?= htmlspecialchars($comunidade["username"]) ?></p>

        <?php if (!$isMembro): ?>
            <form action="entrar_comunidade.php" method="POST" style="margin:15px 0;">
                <input type="hidden" name="id_comunidade" value="<?= $comunidade['id'] ?>" />
                <button type="submit" style="padding:10px 20px; background:#5a2d82; color:#fff; border:none; border-radius:6px; cursor:pointer;">Entrar na Comunidade</button>
            </form>
        <?php else: ?>
            <p style="color:green; font-weight:bold;">Você é membro desta comunidade</p>
        <?php endif; ?>

        <hr style="margin:20px 0;">
        <h3>Posts</h3>

        <?php
        $sqlPosts = "SELECT p.*, u.username 
                     FROM posts p 
                     JOIN users u ON u.id = p.id_usuario
                     WHERE p.id_comunidade = ?
                     ORDER BY p.data_criacao DESC";
        $stmtPosts = $conn->prepare($sqlPosts);
        $stmtPosts->bind_param("i", $comunidade['id']);
        $stmtPosts->execute();
        $resultPosts = $stmtPosts->get_result();
        while ($post = $resultPosts->fetch_assoc()):
        ?>
            <div class="post">
                <h4><?= htmlspecialchars($post['titulo']) ?></h4>
                <p><?= nl2br(htmlspecialchars($post['texto'])) ?></p>
                <small>Publicado por @<?= htmlspecialchars($post['username']) ?> em <?= $post['data_criacao'] ?></small>
            </div>
        <?php endwhile; ?>
    </div>
</main>

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

<script>
function abrirMenuPosts() {
    const menu = document.getElementById('menuPosts');
    menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
    menu.style.flexDirection = 'column';
}
function abrirModalBlog() {
    document.getElementById('modalBlog').style.display = 'flex';
}
window.onclick = function(e) {
    const modal = document.getElementById('modalBlog');
    if(e.target === modal) modal.style.display = 'none';
};
</script>

</body>
</html>
