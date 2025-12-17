<?php
header('Content-Type: application/json');

require_once 'conexion.php';

// Obtener usuario actual (simulado, reemplaza por $_SESSION['usuario'] si tienes login)
session_start();
$usuario = isset($_SESSION['id_log']) ? $_SESSION['id_log'] : null; // Use id_log from session

if (!$usuario) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Usuario no autenticado.']);
    exit;
}

// Obtener eventos (los del admin y los del usuario actual)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare(
        "SELECT id, nombre, fecha_inicio AS startDate, fecha_fin AS endDate, hora_inicio AS startTime, hora_fin AS endTime, color, usuario_id AS creado_por FROM eventos WHERE usuario_id = :usuario OR usuario_id = (SELECT id_log FROM login WHERE rol = 'admin') ORDER BY fecha_inicio, hora_inicio"
    );
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($eventos);
    exit;
}

// Recibir datos JSON
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if ($action === 'create') {
    $creado_por = $usuario;
    $stmt = $pdo->prepare(
        "INSERT INTO eventos (nombre, fecha_inicio, fecha_fin, hora_inicio, hora_fin, color, usuario_id)
         VALUES (:nombre, :fecha_inicio, :fecha_fin, :hora_inicio, :hora_fin, :color, :usuario_id) RETURNING id"
    );
    $stmt->bindParam(':nombre', $input['name']);
    $stmt->bindParam(':fecha_inicio', $input['startDate']);
    $stmt->bindParam(':fecha_fin', $input['endDate']);
    $stmt->bindParam(':hora_inicio', $input['startTime']);
    $stmt->bindParam(':hora_fin', $input['endTime']);
    $stmt->bindParam(':color', $input['color']);
    $stmt->bindParam(':usuario_id', $creado_por);
    $stmt->execute();
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(['success' => true, 'id' => $row['id']]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

if ($action === 'update' && isset($input['id'])) {
    $stmt = $pdo->prepare(
        "UPDATE eventos SET nombre=:nombre, fecha_inicio=:fecha_inicio, fecha_fin=:fecha_fin, hora_inicio=:hora_inicio, hora_fin=:hora_fin, color=:color
         WHERE id=:id AND (usuario_id=:usuario OR usuario_id=(SELECT id_log FROM login WHERE rol = 'admin'))"
    );
    $stmt->bindParam(':nombre', $input['name']);
    $stmt->bindParam(':fecha_inicio', $input['startDate']);
    $stmt->bindParam(':fecha_fin', $input['endDate']);
    $stmt->bindParam(':hora_inicio', $input['startTime']);
    $stmt->bindParam(':hora_fin', $input['endTime']);
    $stmt->bindParam(':color', $input['color']);
    $stmt->bindParam(':id', $input['id']);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    echo json_encode(['success' => $stmt->rowCount() > 0]);
    exit;
}

if ($action === 'delete' && isset($input['id'])) {
    $stmt = $pdo->prepare(
        "DELETE FROM eventos WHERE id=:id AND (usuario_id=:usuario OR usuario_id=(SELECT id_log FROM login WHERE rol = 'admin'))"
    );
    $stmt->bindParam(':id', $input['id']);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    echo json_encode(['success' => $stmt->rowCount() > 0]);
    exit;
}

echo json_encode(['error' => 'Acción no válida']);
