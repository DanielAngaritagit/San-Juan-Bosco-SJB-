<?php
header('Content-Type: application/json');
ini_set('display_errors', 'Off');
error_reporting(0);

require_once 'conexion.php';

try {
    $stmt = $pdo->prepare("
        SELECT 
            id_seccion,
            grado_numero,
            letra_seccion
        FROM 
            tab_grados
        ORDER BY 
            grado_numero, letra_seccion
    ");
    $stmt->execute();
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'cursos' => $cursos]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener cursos: ' . $e->getMessage()]);
}
?>