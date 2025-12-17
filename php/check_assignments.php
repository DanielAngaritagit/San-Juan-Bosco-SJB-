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
echo "    <title>Verificar Asignaciones de Profesores</title>
";
echo "    <style>
";
echo "        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
";
echo "        .container { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
";
echo "        h1 { color: #333; }
";
echo "        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
";
echo "        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
";
echo "        th { background-color: #f2f2f2; }
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
echo "        <h1>Verificación de Asignaciones de Profesores</h1>
";

try {
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception("Database connection (PDO) not established. Check conexion.php.");
    }

    // Fetch assignments
    $stmt = $pdo->query("SELECT tp.nombres, tp.apellidos, tg.grado_numero, tg.letra_seccion, tpc.id_profesor, tpc.id_seccion
                         FROM tab_profesor_curso tpc
                         JOIN tab_profesores tp ON tpc.id_profesor = tp.id_profesor
                         JOIN tab_grados tg ON tpc.id_seccion = tg.id_seccion
                         ORDER BY tp.apellidos, tg.grado_numero, tg.letra_seccion");
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($assignments) > 0) {
        echo "<p class=\"success\">Asignaciones encontradas:</p>\n";
        echo "<table>\n";
        echo "    <thead>\n";
        echo "        <tr>\n";
        echo "            <th>ID Profesor</th>\n";
        echo "            <th>Profesor</th>\n";
        echo "            <th>ID Sección</th>\n";
        echo "            <th>Grado/Sección</th>\n";
        echo "        </tr>\n";
        echo "    </thead>\n";
        echo "    <tbody>\n";
        foreach ($assignments as $assignment) {
            echo "        <tr>\n";
            echo "            <td>" . htmlspecialchars($assignment['id_profesor']) . "</td>\n";
            echo "            <td>" . htmlspecialchars($assignment['nombres'] . ' ' . $assignment['apellidos']) . "</td>\n";
            echo "            <td>" . htmlspecialchars($assignment['id_seccion']) . "</td>\n";
            echo "            <td>" . htmlspecialchars($assignment['grado_numero'] . $assignment['letra_seccion']) . "</td>\n";
            echo "        </tr>\n";
        }
        echo "    </tbody>\n";
        echo "</table>\n";
    } else {
        echo "<p class=\"error\">No se encontraron asignaciones en la tabla 'tab_profesor_curso'.</p>\n";
    }

} catch (Exception $e) {
    echo "<p class=\"error\">Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
} catch (PDOException $e) {
    echo "<p class=\"error\">Error de Base de Datos: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

echo "    </div>\n";
echo "</body>\n";
echo "</html>\n";
?>