<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

include_once __DIR__ . '/../php/conexion.php';

$response = ['success' => false, 'message' => ''];

$input = json_decode(file_get_contents('php://input'), true);

$id_profesor = $input['id_profesor'] ?? null;
$id_grado = $input['id_grado'] ?? null;

if (empty($id_grado)) { // Only check id_grado, as id_profesor can be NULL for this specific case
    $response['message'] = 'ID de sección es requerido para eliminar.';
    echo json_encode($response);
    exit();
}

try {
    $sql = "DELETE FROM profesor_grado WHERE id_grado = :id_grado";
    if ($id_profesor === null) {
        $sql .= " AND id_profesor IS NULL";
    } else {
        $sql .= " AND id_profesor = :id_profesor";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_grado', $id_grado);
    if ($id_profesor !== null) {
        $stmt->bindParam(':id_profesor', $id_profesor);
    }
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $response['success'] = true;
        $response['message'] = 'Asignación eliminada exitosamente.';
    } else {
        $response['message'] = 'No se encontró la asignación para eliminar.';
    }
} catch (PDOException $e) {
    $response['message'] = 'Error al eliminar la asignación: ' . $e->getMessage();
}

echo json_encode($response);
?>