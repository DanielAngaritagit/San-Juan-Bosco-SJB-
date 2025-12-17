<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');
require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error inesperado.'];
$id_profesor = $_GET['id_profesor'] ?? null;

if (!$id_profesor) {
    echo json_encode(['success' => false, 'message' => 'ID de profesor no proporcionado.']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            TRIM(CONCAT(te.nombres, ' ', te.apellido1, ' ', te.apellido2)) AS nombre_estudiante,
            AVG(promedio_materia) AS promedio_general
        FROM (
            SELECT 
                c.id_estud,
                c.id_curso,
                AVG(c.calificacion) AS promedio_materia
            FROM tab_calificaciones c
            WHERE c.id_profesor = :id_profesor
            GROUP BY c.id_estud, c.id_curso
        ) AS promedios_por_materia
        JOIN tab_estudiante te ON promedios_por_materia.id_estud = te.id_ficha
        GROUP BY promedios_por_materia.id_estud, te.nombres, te.apellido1, te.apellido2
        ORDER BY nombre_estudiante
    ");
    $stmt->execute([':id_profesor' => $id_profesor]);
    $rendimiento_estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($rendimiento_estudiantes) {
        $response = ['success' => true, 'data' => $rendimiento_estudiantes];
    } else {
        $response = ['success' => false, 'message' => 'No se encontraron datos de rendimiento para este profesor.'];
    }

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>