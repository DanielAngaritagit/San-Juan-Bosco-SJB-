<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');
require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error inesperado.'];
$id_log = $_GET['id_profesor'] ?? null; // El JS lo envía como id_profesor, pero es id_log

if (!$id_log) {
    echo json_encode(['success' => false, 'message' => 'ID de profesor no proporcionado.']);
    exit;
}

try {
    // 1. Obtener el id_profesor real desde tab_profesores usando el id_log
    $stmt_profesor_id = $pdo->prepare("SELECT id_profesor FROM tab_profesores WHERE id_log = :id_log");
    $stmt_profesor_id->execute([':id_log' => $id_log]);
    $id_profesor = $stmt_profesor_id->fetchColumn();

    if (!$id_profesor) {
        echo json_encode(['success' => false, 'message' => 'No se pudo encontrar el profesor correspondiente.']);
        exit;
    }

    // 2. Usar el id_profesor obtenido para las estadísticas

    // Total estudiantes
    $stmt_estudiantes = $pdo->prepare("
        SELECT COUNT(DISTINCT c.id_estud) AS total_estudiantes
        FROM tab_calificaciones c
        WHERE c.id_profesor = :id_profesor
    ");
    $stmt_estudiantes->execute([':id_profesor' => $id_profesor]);
    $total_estudiantes = $stmt_estudiantes->fetchColumn();

    // Total materias (cursos) -> Ahora devuelve una lista de grados a los que está asignado el profesor
    $stmt_cursos = $pdo->prepare("
        SELECT DISTINCT g.grado_numero, g.letra_seccion
        FROM profesor_grado AS tpc
        JOIN tab_grados AS g ON tpc.id_grado = g.id_seccion
        WHERE tpc.id_profesor = :id_profesor
        ORDER BY g.grado_numero, g.letra_seccion
    ");
    $stmt_cursos->execute([':id_profesor' => $id_profesor]);
    $cursos_list = $stmt_cursos->fetchAll(PDO::FETCH_ASSOC);

    $cursos_display = [];
    if ($cursos_list) {
        foreach ($cursos_list as $curso) {
            $cursos_display[] = $curso['grado_numero'] . '-' . $curso['letra_seccion'];
        }
    }
    $total_materias_str = implode(', ', $cursos_display);

    // Promedio general
    $stmt_promedio = $pdo->prepare("
        SELECT AVG(promedio_estudiante_materia) AS promedio_general
        FROM (
            SELECT AVG(c.calificacion) AS promedio_estudiante_materia
            FROM tab_calificaciones c
            WHERE c.id_profesor = :id_profesor
            GROUP BY c.id_estud, c.id_curso
        ) AS promedios
    ");
    $stmt_promedio->execute([':id_profesor' => $id_profesor]);
    $promedio_general = $stmt_promedio->fetchColumn();

    $response = [
        'success' => true,
        'data' => [
            'total_estudiantes' => $total_estudiantes ?: 0,
            'total_materias' => $total_materias_str ?: 'N/A', // Devuelve string con los cursos
            'promedio_general' => $promedio_general ? round($promedio_general, 2) : '0.0'
        ]
    ];

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>