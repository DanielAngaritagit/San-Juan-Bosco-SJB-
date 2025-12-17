<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
session_start();
header('Content-Type: application/json');

require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error desconocido.', 'data' => []];

try {
    // Verificar el rol del usuario
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'estudiante') {
        $response = ['success' => false, 'message' => 'Acceso denegado. Este recurso es solo para estudiantes.'];
        echo json_encode($response);
        exit;
    }
    
    // Obtener el ID del estudiante directamente de la sesión
    $id_estud_para_consultas = $_SESSION['id_estud'] ?? null;

    if (!$id_estud_para_consultas) {
        $response = ['success' => false, 'message' => 'ID de estudiante no encontrado en la sesión. Por favor, inicie sesión de nuevo.'];
        echo json_encode($response);
        exit;
    }

    // Obtener información básica del estudiante usando el id_estud
    $stmt_student_info = $pdo->prepare("SELECT 
        te.nombres, 
        te.apellido1 AS apellidos, 
        tg.grado_numero, 
        tg.letra_seccion, 
        te.no_documento AS codigo
    FROM tab_estudiante te
    LEFT JOIN tab_grados tg ON te.id_seccion = tg.id_seccion
    WHERE te.id_ficha = :id_estud");
    $stmt_student_info->bindParam(':id_estud', $id_estud_para_consultas);
    $stmt_student_info->execute();
    $student_info = $stmt_student_info->fetch(PDO::FETCH_ASSOC);

    if (!$student_info) {
        $response = ['success' => false, 'message' => 'No se pudo encontrar la información para el ID de estudiante de la sesión.'];
        echo json_encode($response);
        exit;
    }

    // Obtener calificaciones por materia
    $stmt_grades = $pdo->prepare("SELECT 
        tc.nombre_curso AS materia, 
        tcal.calificacion,
        tcal.id_periodo as periodo,
        tcal.tipo_evaluacion,
        TRIM(CONCAT(tp.nombres, ' ', tp.apellidos)) AS profesor_nombre
    FROM tab_calificaciones tcal
    LEFT JOIN tab_cursos tc ON tcal.id_curso = tc.id_curso
    LEFT JOIN tab_profesores tp ON tcal.id_profesor = tp.id_profesor
    WHERE tcal.id_estud = :id_estud_para_consultas
    ORDER BY tc.nombre_curso, tcal.id_periodo");
    $stmt_grades->bindParam(':id_estud_para_consultas', $id_estud_para_consultas);
    $stmt_grades->execute();
    $grades = $stmt_grades->fetchAll(PDO::FETCH_ASSOC);

    // Calcular promedio general correctamente
    $grades_by_course_for_avg = [];
    foreach ($grades as $grade) {
        if (!isset($grades_by_course_for_avg[$grade['materia']])) {
            $grades_by_course_for_avg[$grade['materia']] = ['sum' => 0, 'count' => 0];
        }
        $grades_by_course_for_avg[$grade['materia']]['sum'] += $grade['calificacion'];
        $grades_by_course_for_avg[$grade['materia']]['count']++;
    }

    $total_of_averages = 0;
    $number_of_courses = 0;
    foreach ($grades_by_course_for_avg as $course => $data) {
        $total_of_averages += ($data['sum'] / $data['count']);
        $number_of_courses++;
    }
    $overall_average = $number_of_courses > 0 ? round($total_of_averages / $number_of_courses, 2) : 0;

    // Preparar datos para el gráfico (promedios por materia para empezar)
    $chart_labels = [];
    $chart_data = [];
    $grades_by_course = [];

    foreach ($grades as $grade) {
        if (!isset($grades_by_course[$grade['materia']])) {
            $grades_by_course[$grade['materia']] = ['sum' => 0, 'count' => 0];
        }
        $grades_by_course[$grade['materia']]['sum'] += $grade['calificacion'];
        $grades_by_course[$grade['materia']]['count']++;
    }

    foreach ($grades_by_course as $materia => $data) {
        $chart_labels[] = $materia;
        $chart_data[] = round($data['sum'] / $data['count'], 2);
    }

    // Colores para el gráfico
    $chart_colors = [
        'rgba(78, 121, 167, 0.7)',  // Blue
        'rgba(242, 142, 43, 0.7)',  // Orange
        'rgba(225, 87, 89, 0.7)',   // Red
        'rgba(118, 183, 178, 0.7)', // Teal
        'rgba(89, 161, 79, 0.7)',   // Green
        'rgba(237, 201, 72, 0.7)',  // Yellow
        'rgba(176, 122, 161, 0.7)', // Purple
        'rgba(255, 157, 167, 0.7)', // Pink
        'rgba(156, 117, 95, 0.7)',  // Brown
        'rgba(186, 176, 172, 0.7)', // Gray
        'rgba(241, 147, 156, 0.7)',
        'rgba(211, 114, 149, 0.7)',
        'rgba(181, 90, 139, 0.7)',
        'rgba(152, 75, 126, 0.7)',
        'rgba(122, 66, 110, 0.7)'
    ];

    $chart_labels = array_keys($grades_by_course);
    $chart_data_values = [];
    $background_colors = [];

    $i = 0;
    foreach ($grades_by_course as $materia => $data) {
        $chart_data_values[] = round($data['sum'] / $data['count'], 2);
        $background_colors[] = $chart_colors[$i % count($chart_colors)];
        $i++;
    }

    $response = [
        'success' => true,
        'message' => 'Rendimiento obtenido exitosamente.',
        'data' => [
            'student_info' => $student_info,
            'overall_average' => $overall_average,
            'grades_by_course' => $grades,
            'chart_data' => [
                'labels' => $chart_labels,
                'datasets' => [[
                    'label' => 'Promedio por Materia',
                    'data' => $chart_data_values,
                    'backgroundColor' => $background_colors,
                    'borderColor' => str_replace('0.6', '1', $background_colors),
                    'borderWidth' => 1
                ]]
            ]
        ]
    ];

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>