<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error desconocido.', 'data' => []];

try {
    $stmt = $pdo->query("SELECT id_profesor, nombres, apellidos, especialidad FROM tab_profesores ORDER BY apellidos, nombres");
    $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = ['success' => true, 'message' => 'Profesores obtenidos exitosamente.', 'data' => $profesores];

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>