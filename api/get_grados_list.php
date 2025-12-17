<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');
require_once '../php/conexion.php';

$response = ['success' => false, 'data' => [], 'message' => ''];

try {
    $stmt = $pdo->query("SELECT id_seccion, grado_numero, letra_seccion FROM tab_grados ORDER BY grado_numero, letra_seccion");
    $grados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    error_log("get_grados_list.php: Number of grades found: " . count($grados));

    if ($grados) {
        $response['success'] = true;
        $response['data'] = $grados;
    } else {
        $response['message'] = 'No se encontraron grados en la base de datos.';
    }

} catch (PDOException $e) {
    $response['message'] = 'Error de base de datos: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = 'Error inesperado: ' . $e->getMessage();
}

echo json_encode($response);
?>