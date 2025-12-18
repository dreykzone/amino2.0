<?php
require_once 'auth.php';

$conn = new mysqli("localhost", "root", "", "amino2");

$id_post = intval($_GET['id_post'] ?? 0);
$id_comunidade = intval($_GET['id_comunidade'] ?? 0);

$sql = "SELECT 
    c.*,
    COALESCE(NULLIF(mc.nickname, ''), u.username) AS nome_exibido
FROM comentarios c
JOIN users u ON u.id = c.id_usuario
LEFT JOIN membros_comunidade mc
    ON mc.id_usuario = c.id_usuario
   AND mc.id_comunidade = ?
WHERE c.id_post = ?
ORDER BY c.data_criacao ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_comunidade, $id_post);
$stmt->execute();
$result = $stmt->get_result();


while ($c = $result->fetch_assoc()) {

    echo '
    <div class="comentario">
        <div class="comentario-header">
            <span class="comentario-user">@' . htmlspecialchars($c['nome_exibido']) . '</span>
            <span class="comentario-data">' . date('d/m/Y H:i', strtotime($c['data_criacao'])) . '</span>
        </div>
        <div class="comentario-texto">
            ' . nl2br(htmlspecialchars($c['comentario'])) . '
        </div>
    </div>
    ';
}

?>