<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
session_start();
require_once '../php/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_log'])) {
    echo json_encode(['status' => 'logged_out']);
    exit();
}

$id_log = $_SESSION['id_log'];
$current_session_id = session_id(); // Use the current session_id()

try {
    $stmt = $pdo->prepare("SELECT session_id FROM active_sessions WHERE id_log = :id_log");
    $stmt->bindParam(':id_log', $id_log, PDO::PARAM_INT);
    $stmt->execute();
    $db_session_id = $stmt->fetchColumn();

    if ($db_session_id !== false) {
        if ($db_session_id === $current_session_id) {
            echo json_encode(['status' => 'valid']);
        } else {
            // The session in the database does not match the current one, meaning another session has taken control.
            session_unset();
            session_destroy();
            echo json_encode(['status' => 'invalid']);
        }
    } else {
        // User not found in the database, the session is invalid.
        session_unset();
        session_destroy();
        echo json_encode(['status' => 'invalid']);
    }
} catch (PDOException $e) {
    // Log the error and send a generic error message
    error_log("Database error in check_session_status.php: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error.']);
}
?>