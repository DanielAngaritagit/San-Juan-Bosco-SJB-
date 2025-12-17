<?php
header('Content-Type: text/html; charset=utf-8');
include_once __DIR__ . '/conexion.php'; // Adjust path if necessary

echo "<!DOCTYPE html>\n";
echo "<html lang=\"es\">\n";
echo "<head>\n";
echo "    <meta charset=\"UTF-8\">
";
echo "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
";
echo "    <title>Modificar Esquema de Base de Datos</title>
";
echo "    <style>
";
echo "        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
";
echo "        .container { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
";
echo "        h1 { color: #333; }
";
echo "        .warning { color: orange; font-weight: bold; }
";
echo "        .error { color: red; font-weight: bold; }
";
echo "        .success { color: green; font-weight: bold; }
";
echo "    </style>
";
echo "</head>
";
echo "<body>
";
echo "    <div class=\"container\">
";
echo "        <h1>Modificación de Esquema de Base de Datos</h1>
";
echo "        <p class=\"warning\"><strong>ADVERTENCIA DE SEGURIDAD CRÍTICA:</strong> Este script modifica la estructura de tu base de datos. No es una práctica recomendada ejecutar cambios de esquema a través del navegador en un entorno de producción. Úsalo bajo tu propio riesgo y elimínalo inmediatamente después de su uso.</p>
";
echo "        <p>Para ejecutar la modificación, añade <code>?confirm=true</code> a la URL.</p>
";

if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'true') {
    echo "<p class=\"error\">Confirmación requerida. Por favor, añade <code>?confirm=true</code> a la URL para ejecutar la modificación.</p>\n";
    echo "    </div>\n";
echo "</body>\n";
echo "</html>\n";
    exit();
}

try {
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception("Database connection (PDO) not established. Check conexion.php.");
    }

    $pdo->beginTransaction();

    // PASO 1: Eliminar la tabla existente (si ya existe y tiene la estructura incorrecta)
    echo "<p>Intentando eliminar la tabla 'tab_profesor_curso' si existe...</p>\n";
    $pdo->exec("DROP TABLE IF EXISTS tab_profesor_curso CASCADE;");
    echo "<p class=\"success\">Tabla 'tab_profesor_curso' eliminada (si existía).</p>\n";

    // PASO 2: Crear la tabla con la estructura correcta
    echo "<p>Creando la tabla 'tab_profesor_curso' con la nueva estructura...</p>\n";
    $pdo->exec("\n        CREATE TABLE tab_profesor_curso (\n            id_profesor INT NOT NULL,\n            id_seccion INT NOT NULL,\n            PRIMARY KEY (id_profesor, id_seccion),\n            FOREIGN KEY (id_profesor) REFERENCES tab_profesores(id_profesor) ON DELETE CASCADE,\n            FOREIGN KEY (id_seccion) REFERENCES tab_grados(id_seccion) ON DELETE CASCADE\n        );\n    ");
    echo "<p class=\"success\">Tabla 'tab_profesor_curso' creada exitosamente con la nueva estructura.</p>\n";

    $pdo->commit();
    echo "<p class=\"success\"><strong>¡Modificación del esquema completada exitosamente!</strong></p>\n";
echo "<p class=\"warning\"><strong>¡IMPORTANTE: Elimina este archivo (<code>php/modify_db_schema.php</code>) INMEDIATAMENTE de tu servidor!</strong></p>\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "<p class=\"error\">Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "<p class=\"error\"><strong>La modificación del esquema falló.</strong></p>\n";
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "<p class=\"error\">Error de Base de Datos: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "<p class=\"error\"><strong>La modificación del esquema falló.</strong></p>\n";
}

echo "    </div>\n";
echo "</body>\n";
echo "</html>\n";
?>
