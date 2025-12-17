<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
session_start();
header('Content-Type: application/json');

require_once '../php/conexion.php'; // This brings in $pdo

if (!isset($_SESSION['id_log'])) {
    echo json_encode(['success' => false, 'message' => 'No has iniciado sesión.']);
    exit;
}

$id_log = $_SESSION['id_log'];
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';

if (empty($current_password) || empty($new_password)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit;
}

try {
    // Use the correct $pdo variable
    $query = "SELECT contrasena FROM login WHERE id_log = :id_log";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_log', $id_log, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($current_password, $user['contrasena'])) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE login SET contrasena = :new_password WHERE id_log = :id_log";
        
        // Use the correct $pdo variable
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->bindParam(':new_password', $hashed_password);
        $update_stmt->bindParam(':id_log', $id_log, PDO::PARAM_INT);

        if ($update_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo cambiar la contraseña.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'La contraseña actual es incorrecta.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>