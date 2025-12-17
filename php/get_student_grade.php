<?php
header('Content-Type: application/json');
ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once 'conexion.php';

$document_id = $_GET['document_id'] ?? null;

if (!$document_id) {
    echo json_encode(['success' => false, 'message' => 'ID de documento no proporcionado.']);
    exit();
}

try {
    // 1. Get id_seccion from tab_estudiante using no_documento
    $stmt_get_id_seccion = $pdo->prepare("SELECT id_seccion FROM tab_estudiante WHERE no_documento = :document_id");
    $stmt_get_id_seccion->bindParam(':document_id', $document_id, PDO::PARAM_STR);
    $stmt_get_id_seccion->execute();
    $seccion_data = $stmt_get_id_seccion->fetch(PDO::FETCH_ASSOC);

    if (!$seccion_data || $seccion_data['id_seccion'] === null) {
        echo json_encode(['success' => false, 'message' => 'Grado/sección no asignado para este estudiante.']);
        exit();
    }
    $id_seccion = $seccion_data['id_seccion'];

    // 2. Get grado_numero and letra_seccion from tab_grados using id_seccion
    $stmt_get_grado_details = $pdo->prepare("SELECT grado_numero, letra_seccion FROM tab_grados WHERE id_seccion = :id_seccion");
    $stmt_get_grado_details->bindParam(':id_seccion', $id_seccion, PDO::PARAM_INT);
    $stmt_get_grado_details->execute();
    $grado_details = $stmt_get_grado_details->fetch(PDO::FETCH_ASSOC);

    if (!$grado_details) {
        echo json_encode(['success' => false, 'message' => 'Detalles de grado/sección no encontrados.']);
        exit();
    }

    echo json_encode(['success' => true, 'grado' => $grado_details['grado_numero'], 'seccion' => $grado_details['letra_seccion']]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()]);
}
?>