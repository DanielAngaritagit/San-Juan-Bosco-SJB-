<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');
require_once '../php/conexion.php';

$response = ['success' => false, 'count' => 0, 'message' => ''];

// Get input data
$input = json_decode(file_get_contents('php://input'), true);
$id_grado = $input['id_grado'] ?? null;
$letra_seccion = $input['letra_seccion'] ?? null;

if (empty($id_grado) || empty($letra_seccion)) {
    $response['message'] = 'Faltan parámetros: id_grado o letra_seccion.';
    echo json_encode($response);
    exit();
}

try {
    // Query to count students in the specified grade and section
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tab_estudiante te JOIN tab_grados tg ON te.id_seccion = tg.id_seccion WHERE tg.grado_numero = :id_grado AND tg.letra_seccion = :letra_seccion");
    $stmt->bindParam(':id_grado', $id_grado, PDO::PARAM_INT);
    $stmt->bindParam(':letra_seccion', $letra_seccion, PDO::PARAM_STR);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    $response['success'] = true;
    $response['count'] = $count;

} catch (PDOException $e) {
    $response['message'] = 'Error de base de datos: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = 'Error inesperado: ' . $e->getMessage();
}

echo json_encode($response);
?>