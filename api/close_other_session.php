<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
session_start();
require_once '../php/conexion.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$usuario = $data['usuario'] ?? null;
$rol = $data['rol'] ?? null;

if (!$usuario || !$rol) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario y rol son requeridos.']);
    exit;
}

try {
    // Find the user in the login table
    $stmt = $pdo->prepare("SELECT id_log FROM login WHERE usuario = :usuario");
    $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
    $stmt->execute();
    $id_log = $stmt->fetchColumn();

    if ($id_log) {
        // Regenerate session ID to create a new session for the current browser
        session_regenerate_id(true);
        $new_session_id = session_id();

        // Update the session_id in the database
        $update_stmt = $pdo->prepare("UPDATE login SET session_id_actual = :session_id WHERE id_log = :id_log");
        $update_stmt->bindParam(':session_id', $new_session_id, PDO::PARAM_STR);
        $update_stmt->bindParam(':id_log', $id_log, PDO::PARAM_INT);
        $update_stmt->execute();

        // Set session variables for the new login
        $_SESSION['usuario'] = $usuario;
        $_SESSION['rol'] = $rol;
        $_SESSION['id_log'] = $id_log;

        // Return success
        echo json_encode(['status' => 'success', 'role' => $rol]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
    }
} catch (PDOException $e) {
    // Log the error and send a generic error message
    error_log("Database error in close_other_session.php: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error.']);
}
?>