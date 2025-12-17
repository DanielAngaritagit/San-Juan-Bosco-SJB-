<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error desconocido.', 'data' => []];

try {
    // Obtener las PQR como notificaciones
    $stmt = $pdo->query("SELECT id_pqrsf, descripcion AS mensaje, fecha_creacion AS fecha FROM tab_pqrsf ORDER BY fecha_creacion DESC LIMIT 10");
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = ['success' => true, 'message' => 'Notificaciones obtenidas exitosamente.', 'data' => $notifications];

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>