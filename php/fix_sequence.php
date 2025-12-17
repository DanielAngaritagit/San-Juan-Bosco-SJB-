<?php
// --- Inclusión de Dependencias ---
require_once 'conexion.php';

try {
    // Corregir la secuencia del id_log de la tabla login
    $pdo->exec("SELECT setval('login_id_log_seq', (SELECT MAX(id_log) FROM login)+1);");
    // Corregir la secuencia del id_acudiente de la tabla tab_acudiente
    $pdo->exec("SELECT setval('tab_acudiente_id_acudiente_seq', (SELECT MAX(id_acudiente) FROM tab_acudiente)+1);");

    echo "<h1>Secuencias de las tablas 'login' y 'tab_acudiente' corregidas exitosamente.</h1>";
    echo "<p>Ahora puedes intentar crear el usuario administrador temporal de nuevo.</p>";
    echo "<a href='create_temp_admin.php'>Crear usuario temporal</a>";

} catch (Exception $e) {
    echo "<h1>Error al corregir la secuencia:</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>
