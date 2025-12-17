<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

include_once __DIR__ . '/../php/conexion.php';

$response = ['success' => false, 'message' => ''];

$input = json_decode(file_get_contents('php://input'), true);
error_log("Incoming data for save_periodo_academico: " . print_r($input, true));

$id_periodo = $input['id_periodo'] ?? null;
$nombre_periodo = $input['nombre_periodo'] ?? null;
$fecha_inicio = $input['fecha_inicio'] ?? null;
$fecha_fin = $input['fecha_fin'] ?? null;

if (empty($nombre_periodo) || empty($fecha_inicio) || empty($fecha_fin)) {
    $response['message'] = 'Nombre del periodo, fecha de inicio y fecha de fin son requeridos.';
    echo json_encode($response);
    exit();
}

try {
    if ($id_periodo) {
        // Update existing period
        $stmt = $pdo->prepare("UPDATE periodos_academicos SET nombre_periodo = :nombre_periodo, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin WHERE id_periodo = :id_periodo");
        $stmt->bindParam(':id_periodo', $id_periodo);
        $stmt->bindParam(':nombre_periodo', $nombre_periodo);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->execute();
        if ($stmt->rowCount() > 0) { // Check if any rows were affected
            $response['success'] = true;
            $response['message'] = 'Periodo académico actualizado exitosamente.';
        } else {
            $response['success'] = false;
            $response['message'] = 'No se encontró el periodo académico para actualizar o los datos son los mismos.';
        }
    } else {
        // Insert new period
        $stmt = $pdo->prepare("INSERT INTO periodos_academicos (nombre_periodo, fecha_inicio, fecha_fin) VALUES (:nombre_periodo, :fecha_inicio, :fecha_fin)");
        $stmt->bindParam(':nombre_periodo', $nombre_periodo);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->execute();
        if ($stmt->rowCount() > 0) { // Check if a row was inserted
            $response['success'] = true;
            $response['message'] = 'Periodo académico guardado exitosamente.';
        } else {
            $response['success'] = false;
            $response['message'] = 'Error desconocido al insertar el periodo académico.';
        }
    }
} catch (PDOException $e) {
    $response['message'] = 'Error al guardar el periodo académico: ' . $e->getMessage();
}

echo json_encode($response);
?>