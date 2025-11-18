<?php
session_start();
require_once __DIR__ . "/../login_registro/config.php";
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

// Se for admin, retorna todos; senão apenas os do usuário
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'];
$usuario_id = $_SESSION['id'];

if ($isAdmin) {
    $sql = "SELECT a.id, a.usuario_id, u.nome AS usuario_nome, s.nome AS servico, a.data_agendamento AS data, a.horario, a.status
            FROM agendamentos a
            LEFT JOIN usuarios u ON a.usuario_id = u.id
            LEFT JOIN servicos s ON a.servico_id = s.id
            ORDER BY a.data_agendamento ASC, a.horario ASC";
    $res = $conexao->query($sql);
} else {
    $stmt = $conexao->prepare("SELECT a.id, a.usuario_id, u.nome AS usuario_nome, s.nome AS servico, a.data_agendamento AS data, a.horario, a.status
            FROM agendamentos a
            LEFT JOIN usuarios u ON a.usuario_id = u.id
            LEFT JOIN servicos s ON a.servico_id = s.id
            WHERE a.usuario_id = ?
            ORDER BY a.data_agendamento ASC, a.horario ASC");
    $stmt->bind_param('i', $usuario_id);
    $stmt->execute();
    $res = $stmt->get_result();
}

$rows = [];
while ($row = $res->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode($rows);