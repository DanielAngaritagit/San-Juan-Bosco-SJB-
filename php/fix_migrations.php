<?php
require_once 'conexion.php';

header('Content-Type: text/plain');

function columnExists($pdo, $table, $column) {
    try {
        $stmt = $pdo->prepare("SELECT 1 FROM information_schema.columns WHERE table_name = :table AND column_name = :column");
        $stmt->execute([':table' => $table, ':column' => $column]);
        return $stmt->fetchColumn() !== false;
    } catch (PDOException $e) {
        // Handle potential errors, e.g., table not found
        return false;
    }
}

try {
    $pdo->beginTransaction();

    echo "Iniciando migración de base de datos (versión segura)...
";

    // --- Cambios para tab_profesores ---
    echo "\nModificando tabla: tab_profesores...
";
    if (!columnExists($pdo, 'tab_profesores', 'ciudad_expedicion')) {
        $pdo->exec("ALTER TABLE tab_profesores ADD COLUMN ciudad_expedicion VARCHAR(100);");
        echo "- Columna 'ciudad_expedicion' añadida a tab_profesores.\n";
    } else {
        echo "- Columna 'ciudad_expedicion' ya existe en tab_profesores.\n";
    }
    if (!columnExists($pdo, 'tab_profesores', 'estado_civil')) {
        $pdo->exec("ALTER TABLE tab_profesores ADD COLUMN estado_civil VARCHAR(50);");
        echo "- Columna 'estado_civil' añadida a tab_profesores.\n";
    } else {
        echo "- Columna 'estado_civil' ya existe en tab_profesores.\n";
    }

    // --- Cambios para tab_administradores ---
    echo "\nModificando tabla: tab_administradores...
";
    if (!columnExists($pdo, 'tab_administradores', 'fecha_expedicion')) {
        $pdo->exec("ALTER TABLE tab_administradores ADD COLUMN fecha_expedicion DATE;");
        echo "- Columna 'fecha_expedicion' añadida a tab_administradores.\n";
    } else {
        echo "- Columna 'fecha_expedicion' ya existe en tab_administradores.\n";
    }
    if (!columnExists($pdo, 'tab_administradores', 'alergias')) {
        $pdo->exec("ALTER TABLE tab_administradores ADD COLUMN alergias TEXT;");
        echo "- Columna 'alergias' añadida a tab_administradores.\n";
    } else {
        echo "- Columna 'alergias' ya existe en tab_administradores.\n";
    }

    $pdo->commit();

    echo "\n¡Migración (versión segura) completada exitosamente!\n";

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo "Error durante la migración: " . $e->getMessage();
    exit();
}
?>