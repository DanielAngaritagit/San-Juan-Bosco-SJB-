<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error desconocido.', 'data' => []];

try {
    $course_name = $_GET['course_name'] ?? '';

    if (empty($course_name)) {
        $response = ['success' => false, 'message' => 'Nombre del curso no proporcionado.'];
        echo json_encode($response);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id_curso FROM tab_cursos WHERE nombre_curso = :course_name");
    $stmt->bindParam(':course_name', $course_name);
    $stmt->execute();
    $course_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($course_data) {
        $response = ['success' => true, 'message' => 'ID de curso obtenido exitosamente.', 'data' => $course_data];
    } else {
        $response = ['success' => false, 'message' => 'Curso no encontrado.'];
    }

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>