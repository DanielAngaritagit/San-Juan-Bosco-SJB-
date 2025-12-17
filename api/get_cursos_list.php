<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');
ini_set('display_errors', 'Off');
error_reporting(0);

require_once '../php/conexion.php';

try {
    $stmt = $pdo->prepare("
        SELECT 
            id_curso,
            nombre_curso,
            grado
        FROM 
            tab_cursos
        ORDER BY 
            grado, nombre_curso
    ");
    $stmt->execute();
    $cursos_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'cursos' => $cursos_list]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener la lista de cursos: ' . $e->getMessage()]);
}
?>