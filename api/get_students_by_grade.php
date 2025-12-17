<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error desconocido.', 'data' => []];

try {
    $grade_type = $_GET['grade_type'] ?? '';
    $grade_numbers = [];

    switch ($grade_type) {
        case 'preescolar':
            $grade_numbers = [0]; // Asumiendo grado 0 para preescolar
            break;
        case 'primaria':
            $grade_numbers = [1, 2, 3, 4, 5];
            break;
        case 'secundaria':
            $grade_numbers = [6, 7, 8, 9, 10, 11];
            break;
        default:
            $response = ['success' => false, 'message' => 'Tipo de grado no válido.'];
            echo json_encode($response);
            exit;
    }

    $placeholders = implode(',', array_fill(0, count($grade_numbers), '?'));
    
    $sql = "SELECT 
        te.id_ficha AS id_estud, 
        te.nombres, 
        te.apellido1, 
        te.apellido2, 
        tg.grado_numero, 
        tg.letra_seccion, 
        tc.nombre_curso AS curso_matriculado, 
        tp.nombres AS profesor_nombres, 
        tp.apellidos AS profesor_apellidos
    FROM tab_estudiante te
    LEFT JOIN tab_grados tg ON te.id_seccion = tg.id_seccion
    LEFT JOIN tab_matriculas tm ON te.id_ficha = tm.id_estud
    LEFT JOIN tab_cursos tc ON tm.id_curso = tc.id_curso
    LEFT JOIN tab_profesores tp ON tc.id_curso = tp.id_materia -- Asumiendo que el profesor de la materia es el principal
    WHERE tg.grado_numero IN ($placeholders)
    ORDER BY tg.grado_numero, tg.letra_seccion, te.apellido1, te.nombres";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($grade_numbers);
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = ['success' => true, 'message' => 'Estudiantes obtenidos exitosamente.', 'data' => $estudiantes];

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>