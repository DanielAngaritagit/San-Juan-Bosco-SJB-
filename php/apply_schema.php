<?php
// --- Inclusión de Dependencias ---
require_once 'conexion.php';

try {
    // Leer el contenido del archivo SQL
    $sql = file_get_contents('../scripts/db_institucional.sql');

    if ($sql === false) {
        throw new Exception("No se pudo leer el archivo scripts/db_institucional.sql");
    }

    // Ejecutar las consultas SQL
    $pdo->exec($sql);

    echo "<h1>Esquema de la base de datos aplicado exitosamente.</h1>";
    echo "<p>La base de datos ha sido reseteada con los datos del archivo <code>scripts/db_institucional.sql</code>.</p>";

} catch (Exception $e) {
    echo "<h1>Error al aplicar el esquema de la base de datos:</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>
