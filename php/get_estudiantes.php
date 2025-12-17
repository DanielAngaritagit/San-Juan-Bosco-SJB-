<?php
header('Content-Type: application/json');
ini_set('display_errors', 'Off');
error_reporting(0);

// Incluir el archivo de conexión a la base de datos
require_once 'conexion.php';

try {
    // Consulta para obtener el id, nombre completo y email de los estudiantes
    $stmt = $pdo->prepare("
        SELECT 
            id_ficha AS id_estud,
            nombres,
            apellido1,
            apellido2,
            email
        FROM 
            tab_estudiante
        ORDER BY 
            apellido1, apellido2, nombres
    ");
    $stmt->execute();
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'estudiantes' => $estudiantes]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener estudiantes: ' . $e->getMessage()]);
}
?>