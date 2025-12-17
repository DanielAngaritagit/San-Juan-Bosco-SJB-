<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');
require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error inesperado.'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            c.fecha, 
            TRIM(CONCAT(te.nombres, ' ', te.apellido1, ' ', te.apellido2)) AS nombre_estudiante, 
            cr.nombre_curso AS nombre_materia, 
            CONCAT(g.grado_numero, '° ', g.letra_seccion) AS nombre_grado,
            c.tipo_evaluacion, 
            c.calificacion, 
            c.comentario, 
            p.nombre_periodo as periodo 
        FROM tab_calificaciones c
        LEFT JOIN tab_estudiante te ON c.id_estud = te.id_ficha
        LEFT JOIN tab_cursos cr ON c.id_curso = cr.id_curso
        LEFT JOIN tab_grados g ON te.id_seccion = g.id_seccion
        LEFT JOIN periodos_academicos p ON c.id_periodo = p.id_periodo
        ORDER BY c.fecha DESC, c.id_calificacion DESC
    ");
    $stmt->execute();
    $calificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($calificaciones) {
        $response['success'] = true;
        $response['data'] = $calificaciones;
    } else {
        $response['success'] = true;
        $response['data'] = [];
        $response['message'] = 'No se encontraron calificaciones.';
    }

} catch (PDOException $e) {
    error_log("get_calificaciones_recientes.php: PDOException: " . $e->getMessage());
    $response['success'] = false;
    $response['message'] = 'Error de base de datos: ' . $e->getMessage();
}

echo json_encode($response);
