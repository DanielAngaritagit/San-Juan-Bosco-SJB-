<?php
header('Content-Type: application/json');
session_start();

require_once '../php/conexion.php';

$response = ['status' => 'error', 'message' => 'No se pudo procesar la solicitud.', 'conversations' => []];

$id_usuario = session_id();

// Ensure session_id() is not empty
if (empty($id_usuario)) {
    $response['message'] = 'No se pudo generar un ID de sesión para usuario anónimo.';
    echo json_encode($response);
    exit();
}

try {
    $conn = $pdo; // Use the global $pdo connection object
    // Seleccionar conversaciones del usuario en las últimas 24 horas
    $sql = "SELECT mensaje, emisor, fecha_hora FROM tab_chatbot_conversations WHERE id_usuario = :id_usuario AND fecha_hora >= NOW() - INTERVAL '24 hours' ORDER BY fecha_hora ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['status'] = 'success';
    $response['message'] = 'Conversaciones recuperadas correctamente.';
    $response['conversations'] = $conversations;

} catch (PDOException $e) {
    $response['message'] = 'Error de base de datos: ' . $e->getMessage();
} finally {
    // No cerrar $conn aquí si es el objeto global $pdo, ya que podría ser usado por otros scripts.
    // PHP cerrará la conexión automáticamente al finalizar el script.
}

echo json_encode($response);
?>