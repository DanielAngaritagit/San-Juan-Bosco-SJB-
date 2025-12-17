<?php
require_once 'conexion.php';

echo "<h1>Verificando usuario en la tabla login...</h1>";

try {
    // Buscar por id_log
    $id_log_to_check = 2;
    $stmt_id_log = $conn->prepare("SELECT id_log, usuario, rol FROM login WHERE id_log = ?");
    $stmt_id_log->execute([$id_log_to_check]);
    $user_by_id_log = $stmt_id_log->fetch(PDO::FETCH_ASSOC);

    if ($user_by_id_log) {
        echo "<p>Usuario encontrado por ID de Login ({$id_log_to_check}):</p>";
        echo "<pre>" . print_r($user_by_id_log, true) . "</pre>";
    } else {
        echo "<p>No se encontró usuario con ID de Login: {$id_log_to_check}</p>";
    }

    echo "<hr>";

    // Buscar por usuario (no_documento)
    $usuario_to_check = '12345679';
    $stmt_usuario = $conn->prepare("SELECT id_log, usuario, rol FROM login WHERE usuario = ?");
    $stmt_usuario->execute([$usuario_to_check]);
    $user_by_usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

    if ($user_by_usuario) {
        echo "<p>Usuario encontrado por Usuario (no_documento) ({$usuario_to_check}):</p>";
        echo "<pre>" . print_r($user_by_usuario, true) . "</pre>";
    } else {
        echo "<p>No se encontró usuario con Usuario (no_documento): {$usuario_to_check}</p>";
    }

} catch (PDOException $e) {
    echo "<h2 style='color:red; font-weight:bold;'>Error de base de datos: " . $e->getMessage() . "</h2>";
} catch (Exception $e) {
    echo "<h2 style='color:red; font-weight:bold;'>Error general: " . $e->getMessage() . "</h2>";
}
?>