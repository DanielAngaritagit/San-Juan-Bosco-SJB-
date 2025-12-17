<?php
require_once 'conexion.php';

if ($argc < 3) {
    echo "Uso: php reset_sequence.php <table_name> <id_column_name>\n";
    exit(1);
}

$tableName = $argv[1];
$idColumnName = $argv[2];

try {
    $pdo->beginTransaction();

    // Get the maximum ID from the table
    $stmt = $pdo->query("SELECT MAX({$idColumnName}) FROM {$tableName}");
    $maxId = $stmt->fetchColumn();

    // Get the sequence name
    $stmt = $pdo->query("SELECT pg_get_serial_sequence('{$tableName}', '{$idColumnName}')");
    $sequenceName = $stmt->fetchColumn();

    if (!$sequenceName) {
        throw new Exception("No se encontró la secuencia para la tabla {$tableName} y columna {$idColumnName}.");
    }

    // Reset the sequence
    $newVal = ($maxId === null) ? 1 : $maxId + 1;
    $pdo->exec("ALTER SEQUENCE {$sequenceName} RESTART WITH {$newVal}");

    $pdo->commit();

    echo "Secuencia para {$tableName}.{$idColumnName} reiniciada exitosamente a {$newVal}.\n";

} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Error de base de datos: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

