<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error desconocido.'];

try {
    $input = json_decode(file_get_contents('php://input'), true);

    $id_actividad = $input['id_actividad'] ?? null;
    $id_estud = $input['id_estud'] ?? null;
    $id_profesor = $input['id_profesor'] ?? null; // Asumir que viene del frontend o sesión
    $nombre = $input['nombre'] ?? null; // Tipo de actividad
    $fecha_creacion = $input['fecha'] ?? date('Y-m-d H:i:s');
    $estado = $input['puntaje'] ?? null; // Usar puntaje como estado temporalmente

    if (empty($id_estud) || empty($id_profesor) || empty($nombre) || empty($estado)) {
        $response = ['success' => false, 'message' => 'Faltan datos obligatorios para guardar la actividad.'];
        echo json_encode($response);
        exit;
    }

    if ($id_actividad) {
        // Actualizar actividad existente
        $stmt = $pdo->prepare("UPDATE eventos SET nombre = :nombre, creado = :creado, estado = :estado, descripcion = :descripcion, comentarios = :comentarios WHERE id = :id");
        $stmt->execute([
            ':nombre' => $nombre,
            ':creado' => $fecha_creacion, // Mapping fecha_creacion to creado
            ':estado' => $estado,
            ':descripcion' => $input['descripcion'] ?? null,
            ':comentarios' => $input['comentarios'] ?? null,
            ':id' => $id_actividad // Mapping id_actividad to id
        ]);
        $response = ['success' => true, 'message' => 'Actividad actualizada exitosamente.'];
    } else {
        // Insertar nueva actividad
        // Assuming usuario_id for activities is the id_log of the professor
        // You might need to fetch the id_log of the professor if not directly available
        $profesor_login_id = null;
        if ($id_profesor) {
            $stmt_prof_log = $pdo->prepare("SELECT id_log FROM tab_profesores WHERE id_profesor = :id_profesor");
            $stmt_prof_log->execute([':id_profesor' => $id_profesor]);
            $profesor_login_id = $stmt_prof_log->fetchColumn();
        }

        $stmt = $pdo->prepare("INSERT INTO eventos (usuario_id, tipo_evento, id_profesor, nombre, descripcion, creado, estado, comentarios, archivo_adjunto) VALUES (:usuario_id, 'actividad', :id_profesor, :nombre, :descripcion, :creado, :estado, :comentarios, :archivo_adjunto)");
        $stmt->execute([
            ':usuario_id' => $profesor_login_id,
            ':id_profesor' => $id_profesor,
            ':nombre' => $nombre,
            ':descripcion' => $input['descripcion'] ?? null,
            ':creado' => $fecha_creacion,
            ':estado' => $estado,
            ':comentarios' => $input['comentarios'] ?? null,
            ':archivo_adjunto' => null
        ]);
        $response = ['success' => true, 'message' => 'Actividad guardada exitosamente.'];
    }

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>