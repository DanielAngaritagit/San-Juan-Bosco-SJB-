<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

include_once __DIR__ . '/../php/conexion.php';

$response = ['success' => false, 'data' => [], 'message' => ''];

try {
            $stmt = $pdo->query("SELECT tp.id_profesor, tp.nombres, tp.apellidos, tp.especialidad FROM tab_profesores tp JOIN login l ON tp.id_log = l.id_log WHERE l.rol = 'profesor' ORDER BY tp.apellidos, tp.nombres");
    $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['success'] = true;
    $response['data'] = $profesores;
} catch (PDOException $e) {
    $response['message'] = 'Error al obtener la lista de profesores: ' . $e->getMessage();
}

echo json_encode($response);
?>