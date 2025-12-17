<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

include_once __DIR__ . '/../php/conexion.php';

$response = ['success' => false, 'message' => ''];

$input = json_decode(file_get_contents('php://input'), true);

$id_periodo = $input['id_periodo'] ?? null;

if (empty($id_periodo)) {
    $response['message'] = 'ID de periodo es requerido para eliminar.';
    echo json_encode($response);
    exit();
}

try {
    $pdo->beginTransaction(); // Iniciar transacción

    // 1. Eliminar calificaciones asociadas al periodo
    $stmt_calificaciones = $pdo->prepare("DELETE FROM tab_calificaciones WHERE id_periodo = :id_periodo");
    $stmt_calificaciones->bindParam(':id_periodo', $id_periodo);
    $stmt_calificaciones->execute();

    // 2. Eliminar el periodo académico
    $stmt_periodo = $pdo->prepare("DELETE FROM periodos_academicos WHERE id_periodo = :id_periodo");
    $stmt_periodo->bindParam(':id_periodo', $id_periodo);
    $stmt_periodo->execute();

    if ($stmt_periodo->rowCount() > 0) {
        $pdo->commit(); // Confirmar transacción si todo fue exitoso
        $response['success'] = true;
        $response['message'] = 'Periodo académico y calificaciones asociadas eliminados exitosamente.';
    } else {
        $pdo->rollBack(); // Revertir transacción si no se encontró el periodo
        $response['message'] = 'No se encontró el periodo académico para eliminar.';
    }
} catch (PDOException $e) {
    $pdo->rollBack(); // Revertir transacción en caso de error
    $response['message'] = 'Error al eliminar el periodo académico: ' . $e->getMessage();
}

echo json_encode($response);
?>