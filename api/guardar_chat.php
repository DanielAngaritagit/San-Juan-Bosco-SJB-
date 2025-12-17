<?php
header('Content-Type: application/json');
session_start();

require_once '../php/conexion.php';

$response = ['status' => 'error', 'message' => 'No se pudo procesar la solicitud.'];

// Obtener el cuerpo de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

if ($data && isset($data['message']) && isset($data['sender'])) {
    $mensaje = $data['message'];
    $emisor = $data['sender'];

    // Obtener datos de la sesión si existen
    $id_usuario = session_id(); // Siempre usar session_id() para usuarios no logueados
    $rol = null; // Rol es nulo para usuarios no logueados

    // Asegurarse de que session_id() no esté vacío
    if (empty($id_usuario)) {
        $response['message'] = 'No se pudo generar un ID de sesión para usuario anónimo.';
        echo json_encode($response);
        exit();
    }

    try {
        $conn = $pdo; // Use the global $pdo connection object
        $sql = "INSERT INTO tab_chatbot_conversations (id_usuario, mensaje, emisor) VALUES (:id_usuario, :mensaje, :emisor)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':mensaje', $mensaje);
        $stmt->bindParam(':emisor', $emisor);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Mensaje guardado correctamente.';
        } else {
            $response['message'] = 'Error al guardar el mensaje en la base de datos.';
        }
    } catch (PDOException $e) {
        $response['message'] = 'Error de base de datos: ' . $e->getMessage();
    } finally {
        $conn = null;
    }
} else {
    $response['message'] = 'Datos incompletos.';
}

echo json_encode($response);
?>