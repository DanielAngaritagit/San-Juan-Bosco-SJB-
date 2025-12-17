<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

include_once __DIR__ . '/../php/conexion.php';

$response = ['success' => false, 'data' => [], 'message' => ''];

try {
        $stmt = $pdo->query("SELECT id_periodo, nombre_periodo, fecha_inicio, fecha_fin FROM periodos_academicos ORDER BY fecha_inicio DESC");
    $periodos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['success'] = true;
    $response['data'] = $periodos;
} catch (PDOException $e) {
    $response['message'] = 'Error al obtener los periodos académicos: ' . $e->getMessage();
}

echo json_encode($response);
?>