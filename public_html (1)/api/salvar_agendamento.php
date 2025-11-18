<?php
session_start();
require_once __DIR__ . "/../login_registro/config.php";
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

$usuario_id = $_SESSION['id'];

$servico_id = intval($_POST['servico_id'] ?? 0);
$data = $_POST['data'] ?? '';
$horario = $_POST['horario'] ?? '';

if (!$servico_id || !$data || !$horario) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados incompletos']);
    exit;
}

// Remove strict mode
$conexao->query("SET SESSION sql_mode=''");

// ----------------------------------------
// 🔥 VERIFICAR SE HORÁRIO JÁ ESTÁ OCUPADO
// ----------------------------------------
$check = $conexao->prepare("
    SELECT id FROM agendamentos
    WHERE data_agendamento = ? AND horario = ? AND status <> 'cancelado'
");
$check->bind_param("ss", $data, $horario);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // Já existe agendamento nesse horário
    echo json_encode([
        'error' => 'Este horário já está ocupado. Escolha outro.'
    ]);
    exit;
}

// ----------------------------------------
// SALVAR AGENDAMENTO SE NÃO HOUVER CONFLITO
// ----------------------------------------
$stmt = $conexao->prepare("
    INSERT INTO agendamentos 
    (usuario_id, servico_id, data_agendamento, horario, status, created_at)
    VALUES (?, ?, ?, ?, 'agendado', NOW())
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro no banco (prepare)', 'mysql_error' => $conexao->error]);
    exit;
}

$stmt->bind_param('iiss', $usuario_id, $servico_id, $data, $horario);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
} else {
    http_response_code(500);
    echo json_encode([
        'error' => 'Falha ao salvar agendamento',
        'mysql_error' => $stmt->error
    ]);
}
?>
