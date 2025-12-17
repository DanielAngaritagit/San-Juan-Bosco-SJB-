<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

// Este es un ejemplo de datos. En una aplicación real, estos vendrían de la base de datos.
$enrollments = [
    ['student_name' => 'Estudiante A', 'course_name' => 'Matemáticas', 'date' => '2024-07-20'],
    ['student_name' => 'Estudiante B', 'course_name' => 'Física', 'date' => '2024-07-19'],
    ['student_name' => 'Estudiante C', 'course_name' => 'Matemáticas', 'date' => '2024-07-18'],
];

echo json_encode(['success' => true, 'enrollments' => $enrollments]);
?>