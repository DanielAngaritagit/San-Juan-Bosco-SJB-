<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error desconocido.'];

try {
    $input = json_decode(file_get_contents('php://input'), true);

    $id_pqrsf = $input['id_pqrsf'] ?? null;

    if (empty($id_pqrsf)) {
        $response = ['success' => false, 'message' => 'ID de PQRSF no proporcionado.'];
        echo json_encode($response);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM tab_pqrsf WHERE id_pqrsf = :id_pqrsf");
    $stmt->bindParam(':id_pqrsf', $id_pqrsf);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $response = ['success' => true, 'message' => 'PQRSF eliminada exitosamente.'];
    } else {
        $response = ['success' => false, 'message' => 'PQRSF no encontrada o no se pudo eliminar.'];
    }

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>