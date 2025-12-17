<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error desconocido.', 'data' => []];

try {
    $grado_numero = $_GET['grado_numero'] ?? null;
    $letra_seccion = $_GET['letra_seccion'] ?? null;

    if (empty($grado_numero) || empty($letra_seccion)) {
        $response = ['success' => false, 'message' => 'Grado o sección no proporcionados.'];
        echo json_encode($response);
        exit;
    }

    // Obtener el id_seccion para el grado y sección dados
    $stmt_seccion = $pdo->prepare("SELECT id_seccion FROM tab_grados WHERE grado_numero = :grado_numero AND letra_seccion = :letra_seccion");
    $stmt_seccion->execute([
        ':grado_numero' => $grado_numero,
        ':letra_seccion' => $letra_seccion
    ]);
    $seccion_info = $stmt_seccion->fetch(PDO::FETCH_ASSOC);

    if (!$seccion_info) {
        $response = ['success' => true, 'message' => 'Grado y sección no encontrados.', 'data' => []];
        echo json_encode($response);
        exit;
    }

    $id_seccion = $seccion_info['id_seccion'];

    // Obtener estudiantes de esa sección
    $stmt_estudiantes = $pdo->prepare(
        "SELECT 
            nombres, 
            apellido1, 
            apellido2, 
            no_documento, 
            email 
         FROM tab_estudiante
         WHERE id_seccion = :id_seccion
         ORDER BY apellido1, nombres"
    );
    $stmt_estudiantes->execute([':id_seccion' => $id_seccion]);
    $estudiantes = $stmt_estudiantes->fetchAll(PDO::FETCH_ASSOC);

    $response = ['success' => true, 'message' => 'Estudiantes obtenidos con éxito.', 'data' => $estudiantes];

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>