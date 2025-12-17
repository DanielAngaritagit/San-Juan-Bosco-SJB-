<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error desconocido.', 'is_director' => false];

try {
    $id_log = $_GET['id_profesor'] ?? null; // JS lo envía como id_profesor, pero es id_log

    if (empty($id_log)) {
        $response = ['success' => false, 'message' => 'ID de profesor no proporcionado.'];
        echo json_encode($response);
        exit;
    }

    // 1. Obtener el id_profesor real desde tab_profesores usando el id_log
    $stmt_profesor_id = $pdo->prepare("SELECT id_profesor FROM tab_profesores WHERE id_log = :id_log");
    $stmt_profesor_id->execute([':id_log' => $id_log]);
    $id_profesor = $stmt_profesor_id->fetchColumn();

    if (!$id_profesor) {
        // Si no se encuentra un profesor, no es director de grupo.
        $response = ['success' => true, 'message' => 'Profesor no encontrado.', 'is_director' => false];
        echo json_encode($response);
        exit;
    }

    // 2. Buscar si el profesor es líder de algún grado con el id_profesor real
    $stmt = $pdo->prepare("SELECT grado_numero, letra_seccion FROM tab_grados WHERE profesor_lider_id = :id_profesor");
    $stmt->execute([':id_profesor' => $id_profesor]);
    $grado_info = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($grado_info) {
        $response = [
            'success' => true,
            'message' => 'Profesor es director de grupo.',
            'is_director' => true,
            'grado' => $grado_info['grado_numero'],
            'seccion' => $grado_info['letra_seccion']
        ];
    } else {
        $response = ['success' => true, 'message' => 'Profesor no es director de grupo.', 'is_director' => false];
    }

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>