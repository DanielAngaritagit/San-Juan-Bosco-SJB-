<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error desconocido.', 'data' => []];

try {
    // Asumiendo que los miembros de la junta directiva tienen el rol 'admin' en la tabla login
    $stmt = $pdo->query("SELECT id_log, usuario AS nombres, rol AS especialidad FROM login WHERE rol = 'admin' ORDER BY usuario");
    $junta_directiva = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = ['success' => true, 'message' => 'Junta Directiva obtenida exitosamente.', 'data' => $junta_directiva];

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>