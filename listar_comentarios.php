<?php
$conn = new mysqli("localhost", "root", "", "amino2");
$id_post = intval($_GET['id_post'] ?? 0);

$sql = "SELECT c.comentario, u.username, c.data_criacao 
        FROM comentarios c 
        JOIN users u ON u.id = c.id_usuario 
        WHERE c.id_post = ? 
        ORDER BY c.data_criacao ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_post);
$stmt->execute();
$result = $stmt->get_result();

while ($c = $result->fetch_assoc()) {

    echo '
    <div class="comentario">
        <div class="comentario-header">
            <span class="comentario-user">@' . htmlspecialchars($c['username']) . '</span>
            <span class="comentario-data">' . date('d/m/Y H:i', strtotime($c['data_criacao'])) . '</span>
        </div>
        <div class="comentario-texto">
            ' . nl2br(htmlspecialchars($c['comentario'])) . '
        </div>
    </div>
    ';
}

?>