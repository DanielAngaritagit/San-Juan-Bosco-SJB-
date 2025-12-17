<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/conexion.php';

try {
    $sql = "SELECT nombre AS titulo, fecha_inicio AS fecha, descripcion AS detalles FROM eventos WHERE fecha_inicio BETWEEN CURRENT_DATE AND CURRENT_DATE + INTERVAL '15 days' ORDER BY fecha_inicio ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($eventos);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>