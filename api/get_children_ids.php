<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');
session_start();

require_once '../php/conexion.php';

// Determine the id_acudiente to use
$id_acudiente_to_query = null;

if (isset($_SESSION['is_acudiente']) && $_SESSION['is_acudiente'] && isset($_SESSION['id_acudiente'])) {
    // User is Admin/Profesor with acudiente privileges
    $id_acudiente_to_query = $_SESSION['id_acudiente'];
} elseif (isset($_SESSION['id_log']) && $_SESSION['rol'] === 'padre') {
    // User is logged in directly as a 'padre'
    // We need to fetch id_acudiente from tab_acudiente using id_log
    try {
        $stmt_get_acudiente_id = $pdo->prepare("SELECT id_acudiente FROM tab_acudiente WHERE id_log = :id_log LIMIT 1");
        $stmt_get_acudiente_id->bindParam(':id_log', $_SESSION['id_log'], PDO::PARAM_INT);
        $stmt_get_acudiente_id->execute();
        $acudiente_data = $stmt_get_acudiente_id->fetch(PDO::FETCH_ASSOC);
        if ($acudiente_data) {
            $id_acudiente_to_query = $acudiente_data['id_acudiente'];
        }
    } catch (PDOException $e) {
        error_log('Error al obtener id_acudiente para padre: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error de base de datos al obtener ID de acudiente.']);
        exit;
    }
}

// If no id_acudiente could be determined, deny access
if (is_null($id_acudiente_to_query)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado o ID de acudiente no encontrado.']);
    exit;
}

try {
    $sql = "
        SELECT
            te.id_ficha as id_estud,
            te.nombres,
            te.apellido1,
            te.apellido2,
            te.no_documento
        FROM
            tab_estudiante te
        WHERE
            te.id_acudiente = :id_acudiente_to_query;
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_acudiente_to_query', $id_acudiente_to_query, PDO::PARAM_INT);
    $stmt->execute();

    $children = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($children) {
        echo json_encode(['success' => true, 'data' => $children]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontraron hijos asociados a este padre.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    error_log('Error en get_children_ids.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error de base de datos al obtener los hijos.']);
}
?>