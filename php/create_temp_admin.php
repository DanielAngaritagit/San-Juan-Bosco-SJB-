<?php
// --- Inclusión de Dependencias ---
require_once 'conexion.php';

try {
    // Definir los datos del administrador temporal
    $temp_user = 'admin_temp';
    $temp_pass = 'admin123';
    $temp_rol = 'admin';

    // Verificar si el usuario ya existe
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM login WHERE usuario = ?");
    $stmtCheck->execute([$temp_user]);
    
    if ($stmtCheck->fetchColumn() > 0) {
        throw new Exception("El usuario temporal '{$temp_user}' ya existe.");
    }

    // Reset the sequence for the login table to prevent duplicate key errors
    $pdo->exec("SELECT setval('login_id_log_seq', (SELECT COALESCE(MAX(id_log), 1) FROM login), false);");

    // Hashear la contraseña
    $hashed_password = password_hash($temp_pass, PASSWORD_DEFAULT);

    // Insertar el nuevo usuario administrador
    $stmt = $pdo->prepare("INSERT INTO login (usuario, contrasena, rol) VALUES (?, ?, ?)");
    $stmt->execute([$temp_user, $hashed_password, $temp_rol]);

    echo "<h1>Usuario administrador temporal creado exitosamente.</h1>";
    echo "<p><strong>Usuario:</strong> {$temp_user}</p>";
    echo "<p><strong>Contraseña:</strong> {$temp_pass}</p>";

} catch (Exception $e) {
    echo "<h1>Error al crear el usuario temporal:</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>
