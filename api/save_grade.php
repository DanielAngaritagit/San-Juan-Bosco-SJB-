<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error desconocido.'];

/**
 * Busca el ID del periodo académico correspondiente a una fecha dada.
 * @param string $dateString La fecha en formato YYYY-MM-DD.
 * @param PDO $pdo La instancia de conexión a la base de datos.
 * @return int|null El ID del periodo o null si no se encuentra.
 */
function getPeriodoIdPorFecha($dateString, $pdo) {
    $stmt = $pdo->prepare(
        "SELECT id_periodo FROM periodos_academicos WHERE :fecha BETWEEN fecha_inicio AND fecha_fin LIMIT 1"
    );
    $stmt->execute([':fecha' => $dateString]);
    $periodo = $stmt->fetch(PDO::FETCH_ASSOC);
    return $periodo ? (int)$periodo['id_periodo'] : null;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);

    $id_estud = $input['id_estud'] ?? null;
    $id_profesor = $input['id_profesor'] ?? null;
    $id_curso = $input['id_curso'] ?? null;
    $tipo_evaluacion = $input['tipo_evaluacion'] ?? null;
    $calificacion = $input['calificacion'] ?? null;
    $fecha = $input['fecha'] ?? null;
    $comentario = $input['comentario'] ?? '';

    if (empty($id_estud) || empty($id_profesor) || empty($id_curso) || empty($tipo_evaluacion) || is_null($calificacion) || empty($fecha)) {
        $response = ['success' => false, 'message' => 'Faltan datos obligatorios.'];
        echo json_encode($response);
        exit;
    }

    // Obtener el ID del periodo académico
    $id_periodo = getPeriodoIdPorFecha($fecha, $pdo);

    if (is_null($id_periodo)) {
        $response = ['success' => false, 'message' => 'No se encontró un periodo académico activo para la fecha seleccionada.'];
        echo json_encode($response);
        exit;
    }

    // Intentar actualizar primero, ya que es un caso común
    $stmt = $pdo->prepare(
        "UPDATE tab_calificaciones SET calificacion = :calificacion, comentario = :comentario, id_profesor = :id_profesor " .
        "WHERE id_estud = :id_estud AND id_curso = :id_curso AND tipo_evaluacion = :tipo_evaluacion AND id_periodo = :id_periodo"
    );
    
    $stmt->execute([
        ':calificacion' => $calificacion,
        ':comentario' => $comentario,
        ':id_profesor' => $id_profesor,
        ':id_estud' => $id_estud,
        ':id_curso' => $id_curso,
        ':tipo_evaluacion' => $tipo_evaluacion,
        ':id_periodo' => $id_periodo
    ]);

    // Si no se actualizó ninguna fila, significa que no existía, entonces la insertamos
    if ($stmt->rowCount() === 0) {
        $stmt_insert = $pdo->prepare(
            "INSERT INTO tab_calificaciones (id_estud, id_profesor, id_curso, tipo_evaluacion, calificacion, fecha, comentario, id_periodo) " .
            "VALUES (:id_estud, :id_profesor, :id_curso, :tipo_evaluacion, :calificacion, :fecha, :comentario, :id_periodo)"
        );
        $stmt_insert->execute([
            ':id_estud' => $id_estud,
            ':id_profesor' => $id_profesor,
            ':id_curso' => $id_curso,
            ':tipo_evaluacion' => $tipo_evaluacion,
            ':calificacion' => $calificacion,
            ':fecha' => $fecha,
            ':comentario' => $comentario,
            ':id_periodo' => $id_periodo
        ]);
        $response = ['success' => true, 'message' => 'Calificación guardada exitosamente.'];
    } else {
        $response = ['success' => true, 'message' => 'Calificación actualizada exitosamente.'];
    }

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>