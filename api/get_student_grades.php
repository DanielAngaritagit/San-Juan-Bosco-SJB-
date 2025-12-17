<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error desconocido.', 'data' => []];

try {
    // Asumir un ID de estudiante por ahora. En un sistema real, vendría de la sesión del padre.
    $student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 1; 

    // Obtener información del estudiante
    $stmt_student = $pdo->prepare("SELECT 
        te.nombres, 
        CONCAT(te.apellido1, ' ', te.apellido2) AS apellidos, 
        te.fecha_nacimiento, 
        tg.grado_numero, 
        tg.letra_seccion,
        l.foto_url
    FROM tab_estudiante te
    LEFT JOIN tab_grados tg ON te.id_seccion = tg.id_seccion
    LEFT JOIN login l ON l.id_log = te.id_ficha
    WHERE te.id_ficha = :student_id");
    $stmt_student->bindParam(':student_id', $student_id);
    $stmt_student->execute();
    $student_info = $stmt_student->fetch(PDO::FETCH_ASSOC);

    if (!$student_info) {
        $response = ['success' => false, 'message' => 'Estudiante no encontrado.'];
        echo json_encode($response);
        exit;
    }

    // Construir la ruta de la foto del estudiante desde la base de datos
    $student_photo_url = $student_info['foto_url'];
    if ($student_photo_url && strpos($student_photo_url, '/') !== 0 && strpos($student_photo_url, 'http') !== 0) {
        // Si es una ruta relativa como 'uploads/profile_XX.png', anteponer /sjb/
        $student_photo_url = '/sjb/' . $student_photo_url;
    }
    $student_info['profile_pic'] = $student_photo_url ? $student_photo_url : '/sjb/multimedia/pagina_principal/usuario.png';

    // Calcular edad
    $fecha_nacimiento = new DateTime($student_info['fecha_nacimiento']);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nacimiento)->y;
    $student_info['edad'] = $edad;

    // Obtener calificaciones del estudiante
    $stmt_grades = $pdo->prepare("SELECT 
        tc.nombre_curso AS materia, 
        tp.nombres AS profesor_nombres, 
        tp.apellidos AS profesor_apellidos, 
        tcal.calificacion,
        tcal.tipo_evaluacion,
        tcal.fecha,
        tcal.comentario,
        tp.id_profesor,
        tc.id_curso
    FROM tab_calificaciones tcal
    LEFT JOIN tab_cursos tc ON tcal.id_curso = tc.id_curso
    LEFT JOIN tab_profesores tp ON tcal.id_profesor = tp.id_profesor
    WHERE tcal.id_estud = :student_id
    ORDER BY materia, tcal.fecha DESC");
    $stmt_grades->bindParam(':student_id', $student_id);
    $stmt_grades->execute();
    $grades = $stmt_grades->fetchAll(PDO::FETCH_ASSOC);

    // --- Procesamiento para la nueva estructura anidada (ahora por materia directamente) ---
    $grades_by_materia = [];
    $calificaciones_por_materia_para_resumen = [];

    foreach ($grades as $grade) {
        $materia = $grade['materia'];

        if (!isset($grades_by_materia[$materia])) {
            $grades_by_materia[$materia] = [
                'profesor' => trim($grade['profesor_nombres'] . ' ' . $grade['profesor_apellidos']),
                'grades' => []
            ];
        }
        $grades_by_materia[$materia]['grades'][] = [
            'calificacion' => $grade['calificacion'],
            'tipo_evaluacion' => $grade['tipo_evaluacion'],
            'fecha' => $grade['fecha'],
            'comentario' => $grade['comentario'],
            'id_profesor' => $grade['id_profesor'],
            'id_curso' => $grade['id_curso']
        ];

        // Llenar la estructura para el resumen (mejor/peor materia)
        if (!isset($calificaciones_por_materia_para_resumen[$materia])) {
            $calificaciones_por_materia_para_resumen[$materia] = ['total' => 0, 'count' => 0];
        }
        $calificaciones_por_materia_para_resumen[$materia]['total'] += $grade['calificacion'];
        $calificaciones_por_materia_para_resumen[$materia]['count']++;
    }

    // --- Calcular resumen (promedio general, mejor/peor materia) ---
    $mejor_materia = ['nombre' => 'N/A', 'calificacion' => 0];
    $peor_materia = ['nombre' => 'N/A', 'calificacion' => 5.1];
    $total_promedios = 0;
    $num_materias = 0;

    foreach ($calificaciones_por_materia_para_resumen as $materia => $data) {
        $promedio = $data['count'] > 0 ? $data['total'] / $data['count'] : 0;
        if ($promedio > $mejor_materia['calificacion']) {
            $mejor_materia['nombre'] = $materia;
            $mejor_materia['calificacion'] = $promedio;
        }
        if ($promedio < $peor_materia['calificacion']) {
            $peor_materia['nombre'] = $materia;
            $peor_materia['calificacion'] = $promedio;
        }
        $total_promedios += $promedio;
        $num_materias++;
    }

    $promedio_general = $num_materias > 0 ? round($total_promedios / $num_materias, 2) : 0;
    $desempeno_general = 'N/A';
    if ($promedio_general >= 4.5) {
        $desempeno_general = 'Excelente';
    } elseif ($promedio_general >= 3.5) {
        $desempeno_general = 'Bueno';
    } elseif ($promedio_general >= 3.0) {
        $desempeno_general = 'Promedio';
    } else {
        $desempeno_general = 'Bajo';
    }

    $response = [
        'success' => true,
        'message' => 'Datos de estudiante y calificaciones obtenidos exitosamente.',
        'data' => [
            'student_info' => $student_info,
            'grades_by_materia' => $grades_by_materia, // Nueva estructura de datos
            'summary' => [
                'promedio_general' => $promedio_general,
                'mejor_materia' => $mejor_materia,
                'peor_materia' => $peor_materia,
                'desempeno_general' => $desempeno_general
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