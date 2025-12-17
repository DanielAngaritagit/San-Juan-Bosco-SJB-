<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');
require_once '../php/conexion.php';

$response = ['success' => false];
$id_seccion = $_GET['id_seccion'] ?? null;

if (!$id_seccion) {
    echo json_encode(['success' => false, 'message' => 'ID de sección no proporcionado.']);
    exit;
}

try {
    // Query students directly from tab_estudiante based on id_seccion
    $stmt = $pdo->prepare("SELECT id_ficha, nombres, apellido1, apellido2 FROM tab_estudiante WHERE id_seccion = :id_seccion ORDER BY apellido1, apellido2, nombres");
    $stmt->bindParam(':id_seccion', $id_seccion, PDO::PARAM_INT);
    $stmt->execute();
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($estudiantes) {
        $response = ['success' => true, 'estudiantes' => $estudiantes];
    } else {
        $response['message'] = 'No se encontraron estudiantes para esta sección.';
    }

} catch (PDOException $e) {
    $response['message'] = 'Error de base de datos: ' . $e->getMessage();
}

echo json_encode($response);
?>