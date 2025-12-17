<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'conexion.php';

try {
    // Validar y sanitizar el parámetro grado
    $grado_param = isset($_GET['grado']) ? strtolower($_GET['grado']) : '';
    
    $grados_range = [];
    switch ($grado_param) {
        case 'preescolar':
            $grados_range = [0];
            break;
        case 'primaria':
            $grados_range = range(1, 5);
            break;
        case 'secundaria':
            $grados_range = range(6, 11);
            break;
        default:
            throw new Exception('Grado no válido');
    }

    $placeholders = implode(',', array_fill(0, count($grados_range), '?'));

    // Consulta SQL para obtener estudiantes del grado seleccionado
    $stmt = $pdo->prepare("
        SELECT te.id_ficha as id, te.nombres as nombre, te.apellido1 as apellido, tc.nombre_curso as curso
        FROM tab_estudiante te
        LEFT JOIN tab_matriculas tm ON te.id_ficha = tm.id_estud
        LEFT JOIN tab_cursos tc ON tm.id_curso = tc.id_curso
        WHERE CAST(te.grado AS INTEGER) IN ($placeholders)
        ORDER BY tc.nombre_curso, te.apellido1, te.nombres
    ");
    
    $stmt->execute($grados_range);
    
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($estudiantes);
    
} catch(PDOException $e) {
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>