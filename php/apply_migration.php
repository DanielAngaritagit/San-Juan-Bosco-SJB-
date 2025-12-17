<?php
// apply_migration.php

// Incluir la configuración y conexión de la base de datos
require_once 'conexion.php';

echo "Iniciando proceso de migración...\n";

// Lista de archivos de migración a ejecutar
$migration_files = [
    'migration_add_evaluation_types.sql',
    'migration_add_periodo_to_calificaciones.sql',
    'migration_create_periodos_academicos_table.sql',
    'migration_add_transicion_grades.sql',
    'migration_add_foto_url_to_login.sql',
    'migration_add_titulo_to_profesores.sql',
    'migration_add_evaluacion_cognitiva.sql',
    'migration_remove_autoincrement_calificaciones.sql',
    'migration_rename_periodo_nombre_to_tipo.sql',
    'migration_drop_acudiente_phone_columns.sql'
];

foreach ($migration_files as $filename) {
    $migration_file_path = __DIR__ . '/../scripts/' . $filename;

    echo "\nEjecutando migración: " . $filename . "\n";

    // Verificar si el archivo de migración existe
    if (!file_exists($migration_file_path)) {
        echo "Advertencia: El archivo de migración no se encuentra en " . $migration_file_path . ". Saltando.\n";
        continue;
    }

    // Leer el contenido del archivo SQL
    $sql = file_get_contents($migration_file_path);

    if ($sql === false) {
        echo "Error: No se pudo leer el archivo de migración " . $filename . ". Saltando.\n";
        continue;
    }

    // Ejecutar la migración
    try {
        $pdo->exec($sql);
        echo "¡Migración " . $filename . " completada con éxito!\n";
    } catch (PDOException $e) {
        // Capturar error si la tabla ya existe (para IF NOT EXISTS) o si los datos ya existen (ON CONFLICT DO NOTHING)
        if (strpos($e->getMessage(), 'already exists') !== false || strpos($e->getMessage(), 'duplicate key value violates unique constraint') !== false) {
            echo "Advertencia: La tabla o los datos para " . $filename . " ya existen. Continuando.\n";
        } else {
            echo "Error al ejecutar la migración " . $filename . ": " . $e->getMessage() . "\n";
            // Podrías decidir si detener la ejecución o continuar con las siguientes migraciones
        }
    }
}

echo "\nProceso de migración finalizado.\n";

?>