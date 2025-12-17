<?php
header('Content-Type: application/json');

require_once 'conexion.php';

try {
    $stmt = $pdo->query("
        SELECT
            l.id_log,
            te.nombres,
            te.apellido1,
            te.apellido2,
            tc.nombre_curso,
            tc.grado
        FROM
            tab_estudiante te
        JOIN
            login l ON te.no_documento = l.usuario
        JOIN
            tab_matriculas tm ON te.id_ficha = tm.id_estud
        JOIN
            tab_cursos tc ON tm.id_curso = tc.id_curso
        WHERE
            l.rol = 'estudiante'
        ORDER BY
            tc.grado, tc.nombre_curso, te.apellido1, te.nombres
    ");

    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agrupar estudiantes por curso
    $agrupados = [];
    foreach ($estudiantes as $estudiante) {
        $nombre_completo = trim($estudiante['nombres'] . ' ' . $estudiante['apellido1'] . ' ' . $estudiante['apellido2']);
        $curso_grado = $estudiante['grado'] . '-' . $estudiante['nombre_curso'];
        
        if (!isset($agrupados[$curso_grado])) {
            $agrupados[$curso_grado] = [];
        }
        $agrupados[$curso_grado][] = [
            'id_log' => $estudiante['id_log'],
            'nombre' => $nombre_completo,
            'curso' => $estudiante['nombre_curso'],
            'grado' => $estudiante['grado']
        ];
    }

    echo json_encode($agrupados);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al consultar los estudiantes: ' . $e->getMessage()]);
    exit;
}
?>