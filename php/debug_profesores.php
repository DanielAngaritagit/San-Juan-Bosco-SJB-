<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$response = ['success' => false, 'message' => '', 'profesores' => [], 'login_roles' => []];

try {
    // Fetch all professors
    $stmt_profesores = $pdo->query("SELECT id_profesor, nombres, apellidos, especialidad, id_log FROM tab_profesores ORDER BY apellidos, nombres");
    $profesores = $stmt_profesores->fetchAll(PDO::FETCH_ASSOC);
    $response['profesores'] = $profesores;

    // Fetch all login entries with rol 'profesor'
    $stmt_login = $pdo->query("SELECT id_log, usuario, rol FROM login WHERE rol = 'profesor'");
    $login_roles = $stmt_login->fetchAll(PDO::FETCH_ASSOC);
    $response['login_roles'] = $login_roles;

    $response['success'] = true;
    $response['message'] = 'Debug data fetched successfully.';

} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = 'General error: ' . $e->getMessage();
}

echo json_encode($response);
?>