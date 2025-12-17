<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

include_once __DIR__ . '/../php/conexion.php';

$response = ['success' => false, 'message' => ''];

$raw_input = file_get_contents('php://input');
error_log("save_teacher_assignment.php: Raw input: " . $raw_input);

$input = json_decode($raw_input, true);
error_log("save_teacher_assignment.php: Decoded input: " . json_encode($input));

$id_profesor = $input['id_profesor'] ?? null;
$id_grado = $input['id_seccion'] ?? null; // This was the recent fix

error_log("save_teacher_assignment.php: id_profesor received: " . ($id_profesor ?? 'NULL') . ", id_grado received: " . ($id_grado ?? 'NULL'));

try {
    // Check if the assignment already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM profesor_grado WHERE id_profesor = :id_profesor AND id_grado = :id_grado");
    $stmt->bindParam(':id_profesor', $id_profesor, PDO::PARAM_INT); // Explicitly bind as INT
    $stmt->bindParam(':id_grado', $id_grado, PDO::PARAM_INT); // Explicitly bind as INT
    $stmt->execute();
    $exists = $stmt->fetchColumn();

    if ($exists) {
        $response['message'] = 'La asignación ya existe.';
        $response['success'] = true;
    } else {
        $stmt = $pdo->prepare("INSERT INTO profesor_grado (id_profesor, id_grado) VALUES (:id_profesor, :id_grado)");
        $stmt->bindParam(':id_profesor', $id_profesor, PDO::PARAM_INT); // Explicitly bind as INT
        $stmt->bindParam(':id_grado', $id_grado, PDO::PARAM_INT); // Explicitly bind as INT
        $stmt->execute();
        $response['success'] = true;
        $response['message'] = 'Asignación guardada exitosamente.';
    }
} catch (PDOException $e) {
    $response['message'] = 'Error al guardar la asignación: ' . $e->getMessage();
}

echo json_encode($response);
?>