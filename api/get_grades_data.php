<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error desconocido.', 'data' => []];

try {
    $profesor_id = isset($_GET['profesor_id']) ? (int)$_GET['profesor_id'] : 1; // ID de profesor de ejemplo

    // Obtener la asignatura que imparte el profesor
    $stmt_materia = $pdo->prepare("SELECT tm.nombre AS nombre_materia FROM tab_profesores tp JOIN tab_materias tm ON tp.id_materia = tm.id_materia WHERE tp.id_profesor = :profesor_id");
    $stmt_materia->bindParam(':profesor_id', $profesor_id);
    $stmt_materia->execute();
    $materia_profesor = $stmt_materia->fetch(PDO::FETCH_ASSOC);
    $asignatura_instructor = $materia_profesor ? $materia_profesor['nombre_materia'] : 'Asignatura Desconocida';

    // Obtener estudiantes por grado/sección (filtrado por la asignatura del profesor)
    $grado = isset($_GET['grado']) ? (int)$_GET['grado'] : null;
    $seccion = isset($_GET['seccion']) ? $_GET['seccion'] : null;

    $sql_estudiantes = "SELECT 
        te.id_ficha, 
        te.nombres, 
        te.apellido1, 
        te.apellido2, 
        te.id_ficha as id_estud
    FROM tab_matriculas tm
    JOIN tab_cursos tc ON tm.id_curso = tc.id_curso
    JOIN tab_estudiante te ON tm.id_estud = te.id_ficha
    WHERE tc.nombre_curso = :asignatura_instructor";

    if ($grado) {
        $sql_estudiantes .= " AND tc.grado = :grado";
    }
    // No hay seccion en tab_cursos, se asume que el filtro de seccion se aplicaría a tab_grados
    // Si se necesita filtrar por seccion, se debería ajustar la consulta para incluir tab_grados

    $stmt_estudiantes = $pdo->prepare($sql_estudiantes);
    $stmt_estudiantes->bindParam(':asignatura_instructor', $asignatura_instructor);
    if ($grado) {
        $stmt_estudiantes->bindParam(':grado', $grado);
    }
    $stmt_estudiantes->execute();
    $estudiantes = $stmt_estudiantes->fetchAll(PDO::FETCH_ASSOC);

    // Obtener historial de calificaciones
    $sql_calificaciones = "SELECT 
        te.nombres, 
        te.apellido1, 
        te.apellido2, 
        tc.nombre_curso AS asignatura, 
        tcal.calificacion, 
        tcal.comentario, 
        tcal.id_calificacion
    FROM tab_calificaciones tcal
    JOIN tab_estudiante te ON tcal.id_estud = te.id_ficha
    JOIN tab_cursos tc ON tcal.id_curso = tc.id_curso
    WHERE tcal.id_profesor = :profesor_id
    ORDER BY te.apellido1, te.nombres, tc.nombre_curso";
    $stmt_calificaciones = $pdo->prepare($sql_calificaciones);
    $stmt_calificaciones->bindParam(':profesor_id', $profesor_id);
    $stmt_calificaciones->execute();
    $calificaciones = $stmt_calificaciones->fetchAll(PDO::FETCH_ASSOC);

    // Obtener actividades (ahora desde la tabla eventos)
        $sql_actividades = "SELECT 
            e.nombre AS tipo_actividad, 
            e.creado AS fecha, 
            e.estado AS puntaje, 
            e.id AS id_actividad,
            e.descripcion,
            e.comentarios
        FROM eventos e
        WHERE e.id_profesor = :profesor_id AND e.tipo_evento = 'actividad'
        ORDER BY e.creado DESC";
        $stmt_actividades = $pdo->prepare($sql_actividades);
        $stmt_actividades->bindParam(':profesor_id', $profesor_id);
        $stmt_actividades->execute();
        $actividades = $stmt_actividades->fetchAll(PDO::FETCH_ASSOC);

    $response = [
        'success' => true,
        'message' => 'Datos obtenidos exitosamente.',
        'data' => [
            'asignatura_instructor' => $asignatura_instructor,
            'estudiantes' => $estudiantes,
            'calificaciones' => $calificaciones,
            'actividades' => $actividades
        ]
    ];

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>