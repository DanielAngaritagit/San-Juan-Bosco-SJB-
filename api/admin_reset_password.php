<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
session_start();
header('Content-Type: application/json');

// Verificar que el usuario sea administrador
if (!isset($_SESSION['id_log']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado. Se requiere rol de administrador.']);
    exit;
}

require_once '../php/conexion.php';

// Obtener los datos del formulario
$no_documento = $_POST['no_documento'] ?? null;
$new_password = $_POST['new_password'] ?? null;

// Validar que los datos no estén vacíos
if (empty($no_documento) || empty($new_password)) {
    echo json_encode(['success' => false, 'message' => 'El número de documento y la nueva contraseña no pueden estar vacíos.']);
    exit;
}

try {
    // Hashear la nueva contraseña
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Preparar la consulta de actualización
    $stmt = $pdo->prepare("UPDATE login SET contrasena = :contrasena WHERE usuario = :usuario");

    // Ejecutar la consulta
    $stmt->execute([
        ':contrasena' => $hashed_password,
        ':usuario' => $no_documento
    ]);

    // Verificar si se actualizó alguna fila
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'La contraseña para el usuario ' . htmlspecialchars($no_documento) . ' ha sido actualizada exitosamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontró ningún usuario con el número de documento ' . htmlspecialchars($no_documento) . '. No se realizaron cambios.']);
    }

} catch (PDOException $e) {
    // Manejar errores de la base de datos
    error_log("Error al restablecer contraseña: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos al intentar actualizar la contraseña.']);
}
?>