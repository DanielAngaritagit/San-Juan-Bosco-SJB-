<?php
require_once 'conexion.php';

$usuarios = [
    ['usuario' => 'anonimoadmin', 'contrasena' => 'password123', 'rol' => 'administrador'],
    ['usuario' => 'anonimoprofesor', 'contrasena' => 'password123', 'rol' => 'profesor'],
    ['usuario' => 'anonimoestudiante', 'contrasena' => 'password123', 'rol' => 'estudiante'],
    ['usuario' => 'anonimopadre', 'contrasena' => 'password123', 'rol' => 'padre']
];

try {
    $pdo->beginTransaction();

    // Reset the sequence for the login table to prevent duplicate key errors
    $pdo->exec("SELECT setval('login_id_log_seq', (SELECT COALESCE(MAX(id_log), 1) FROM login), false);");

    $stmt = $pdo->prepare("INSERT INTO login (usuario, contrasena, rol) VALUES (:usuario, :contrasena, :rol)");

    foreach ($usuarios as $user) {
        $contrasena_hasheada = password_hash($user['contrasena'], PASSWORD_DEFAULT);
        
        $stmt->execute([
            ':usuario' => $user['usuario'],
            ':contrasena' => $contrasena_hasheada,
            ':rol' => $user['rol']
        ]);
        
        echo "Usuario '" . $user['usuario'] . "' creado con éxito.\n";
    }

    $pdo->commit();

} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Error al crear los usuarios: " . $e->getMessage();
}
?>
