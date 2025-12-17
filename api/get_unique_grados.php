<?php
/**
 * API endpoint para obtener una lista única de números de grado.
 */
header('Content-Type: application/json');
require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'No se pudieron obtener los grados.', 'data' => []];

try {
    $stmt = $pdo->query("SELECT DISTINCT grado_numero FROM tab_grados ORDER BY grado_numero ASC");
    $grados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['message'] = 'Grados obtenidos exitosamente.';
    $response['data'] = $grados;

} catch (Exception $e) {
    $response['message'] = 'Error de base de datos: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
?>