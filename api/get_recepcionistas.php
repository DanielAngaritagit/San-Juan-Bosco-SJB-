<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error desconocido.', 'data' => []];

try {
    // Asumiendo que los recepcionistas pueden ser identificados por un rol o especialidad.
    // Por ahora, obtendremos todos los profesores y luego se puede filtrar.
    $stmt = $pdo->query("SELECT id_profesor, nombres, apellidos, especialidad FROM tab_profesores ORDER BY apellidos, nombres");
    $personal = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = ['success' => true, 'message' => 'Recepcionistas obtenidos exitosamente.', 'data' => $personal];

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>