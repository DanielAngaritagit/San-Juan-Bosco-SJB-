<?php
header('Content-Type: text/plain'); // Set header to plain text for easier debugging

require_once 'conexion.php'; // Assuming conexion.php provides $pdo object

echo "Iniciando la migración...\n";

try {
    echo "Conexión a la base de datos exitosa.\n";

    // Ruta al script SQL de migración
    $sql_file_path = __DIR__ . '/../scripts/migrate_activities_to_events.sql';

    if (!file_exists($sql_file_path)) {
        throw new Exception("El archivo SQL de migración no se encontró en: " . $sql_file_path);
    }

    // Leer el contenido del script SQL
    $sql_commands = file_get_contents($sql_file_path);

    if ($sql_commands === false) {
        throw new Exception("No se pudo leer el contenido del archivo SQL: " . $sql_file_path);
    }

    echo "Leyendo script SQL de migración...\n";

    // Ejecutar los comandos SQL
    // Usar exec() para ejecutar múltiples sentencias SQL
    $pdo->exec($sql_commands);

    echo "Migración completada exitosamente.\n";
    echo "Por favor, verifica la aplicación para confirmar que las actividades se han migrado correctamente.\n";

} catch (PDOException $e) {
    echo "Error de base de datos durante la migración: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// SECURITY WARNING: This script should NOT be publicly accessible on a production server.
// It's intended for development/migration purposes only.
// Consider removing it or restricting access after use.

?>