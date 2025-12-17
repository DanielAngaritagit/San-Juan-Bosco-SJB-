<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

include_once __DIR__ . '/../php/conexion.php';

$response = ['success' => false, 'data' => [], 'message' => ''];

try {
    $stmt = $pdo->query("SELECT id_materia, nombre FROM tab_materias ORDER BY nombre");
    $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['success'] = true;
    $response['data'] = $materias;
} catch (PDOException $e) {
    $response['message'] = 'Error al obtener la lista de materias: ' . $e->getMessage();
}

echo json_encode($response);
?>