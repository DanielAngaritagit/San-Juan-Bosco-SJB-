<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

// Este es un ejemplo de datos. En una aplicación real, estos vendrían de la base de datos.
$records = [
    ['student_name' => 'Estudiante A', 'date' => '2024-07-22', 'status' => 'presente'],
    ['student_name' => 'Estudiante B', 'date' => '2024-07-22', 'status' => 'ausente'],
    ['student_name' => 'Estudiante C', 'date' => '2024-07-21', 'status' => 'presente'],
];

echo json_encode(['success' => true, 'records' => $records]);
?>