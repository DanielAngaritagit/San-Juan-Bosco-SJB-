<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/verificar_sesion.php';
require_once '../php/conexion.php';

if (!isset($_SESSION['id_log'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['pregunta']) || !isset($data['respuesta']) || empty($data['pregunta']) || empty($data['respuesta'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'La pregunta y la respuesta no pueden estar vacías.']);
    exit;
}

$id_log = $_SESSION['id_log'];
$pregunta = trim($data['pregunta']);
$respuesta = trim($data['respuesta']);

// Hashear la respuesta para un almacenamiento seguro
$respuesta_hash = password_hash($respuesta, PASSWORD_DEFAULT);

try {
    // Usamos INSERT ... ON CONFLICT para manejar tanto la inserción como la actualización
    // Esto requiere que la columna id_log tenga una restricción UNIQUE, lo cual hicimos.
    $sql = "
        INSERT INTO tab_seguridad_respuestas (id_log, pregunta, respuesta_hash)
        VALUES (:id_log, :pregunta, :respuesta_hash)
        ON CONFLICT (id_log)
        DO UPDATE SET 
            pregunta = EXCLUDED.pregunta, 
            respuesta_hash = EXCLUDED.respuesta_hash,
            fecha_creacion = CURRENT_TIMESTAMP;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_log', $id_log, PDO::PARAM_INT);
    $stmt->bindParam(':pregunta', $pregunta, PDO::PARAM_STR);
    $stmt->bindParam(':respuesta_hash', $respuesta_hash, PDO::PARAM_STR);
    
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => '¡Tu pregunta de seguridad ha sido guardada con éxito!']);

} catch (PDOException $e) {
    http_response_code(500);
    error_log('Error en set_security_question.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al guardar la pregunta de seguridad.']);
}
?>