<?php
require_once 'conexion.php';

try {
    // Obtener todos los usuarios
    $stmt = $pdo->query("SELECT id_log, usuario, contrasena FROM login");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$users) {
        echo "No se encontraron usuarios en la tabla 'login'.<br>";
        exit;
    }

    echo "Verificando y hasheando contraseñas...<br>";

    foreach ($users as $user) {
        $id_log = $user['id_log'];
        $password = $user['contrasena'];

        // Verificar si la contraseña ya está hasheada (los hashes de bcrypt suelen tener 60 caracteres)
        if (strlen($password) < 60) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Actualizar la contraseña en la base de datos
            $updateStmt = $pdo->prepare("UPDATE login SET contrasena = :hashedPassword WHERE id_log = :id_log");
            $updateStmt->bindParam(':hashedPassword', $hashedPassword, PDO::PARAM_STR);
            $updateStmt->bindParam(':id_log', $id_log, PDO::PARAM_INT);
            $updateStmt->execute();

            echo "Contraseña hasheada para el usuario: " . htmlspecialchars($user['usuario']) . "<br>";
        } else {
            echo "La contraseña para el usuario " . htmlspecialchars($user['usuario']) . " ya parece estar hasheada.<br>";
        }
    }

    echo "<br>Proceso de hasheo completado.<br>";

} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage() . "<br>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
?>