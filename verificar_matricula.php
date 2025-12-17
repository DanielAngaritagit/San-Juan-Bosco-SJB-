<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'php/config.php';

$documento_a_buscar = '1095302732';
$id_curso_esperado = 2; // ID del curso para Grado 7

echo "<html><head><title>Verificación de Matrícula</title>";
echo "<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'>";
echo "<style>body { padding: 20px; font-family: Arial, sans-serif; } .container { max-width: 800px; } .card { margin-bottom: 20px; } .card-header { background-color: #f7f7f7; } </style>";
echo "</head><body><div class='container'>";
echo "<h1><i class='fas fa-search'></i> Verificación de Matrícula para el Documento: " . htmlspecialchars($documento_a_buscar) . "</h1>";

try {
    $pdo = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Buscar el id_estud del estudiante
    echo "<div class='card'><div class='card-header'><h4>Paso 1: Buscar ID del Estudiante</h4></div><div class='card-body'>";
    $stmt_estudiante = $pdo->prepare(
        "SELECT e.id_estud, f.nombres, f.apellido1, f.apellido2 
         FROM tab_estudiante e
         JOIN tab_estudiante f ON e.id_ficha = f.id_ficha
         WHERE f.no_documento = :documento"
    );
    $stmt_estudiante->bindParam(':documento', $documento_a_buscar, PDO::PARAM_STR);
    $stmt_estudiante->execute();
    $estudiante = $stmt_estudiante->fetch(PDO::FETCH_ASSOC);

    if ($estudiante) {
        $id_estud = $estudiante['id_estud'];
        echo "<p class='text-success'><b>Estudiante encontrado:</b></p>";
        echo "<ul>";
        echo "<li><b>ID de Estudiante (id_estud):</b> " . htmlspecialchars($id_estud) . "</li>";
        echo "<li><b>Nombre:</b> " . htmlspecialchars($estudiante['nombres'] . ' ' . $estudiante['apellido1'] . ' ' . $estudiante['apellido2']) . "</li>";
        echo "</ul>";
        echo "</div></div>";

        // 2. Verificar la matrícula
        echo "<div class='card'><div class='card-header'><h4>Paso 2: Verificar Matrícula en el Curso de Grado 7</h4></div><div class='card-body'>";
        $stmt_matricula = $pdo->prepare(
            "SELECT * FROM tab_matriculas WHERE id_estud = :id_estud AND id_curso = :id_curso"
        );
        $stmt_matricula->bindParam(':id_estud', $id_estud, PDO::PARAM_INT);
        $stmt_matricula->bindParam(':id_curso', $id_curso_esperado, PDO::PARAM_INT);
        $stmt_matricula->execute();
        $matricula = $stmt_matricula->fetch(PDO::FETCH_ASSOC);

        if ($matricula) {
            echo "<p class='alert alert-success'><b>¡Matrícula encontrada!</b> El estudiante ya está matriculado en el curso correcto (ID del Curso: " . htmlspecialchars($id_curso_esperado) . ").</p>";
            echo "<p>Si el problema persiste, podría estar relacionado con la caché o algún otro factor en la aplicación.</p>";
        } else {
            echo "<p class='alert alert-danger'><b>Matrícula no encontrada.</b> El estudiante no está matriculado en el curso de Grado 7 (ID del Curso: " . htmlspecialchars($id_curso_esperado) . ").</p>";
            echo "<hr>";
            echo "<h4>Paso 3: Solución Sugerida</h4>";
            echo "<p>Para solucionar el problema, necesita insertar el registro de la matrícula en la base de datos. Ejecute la siguiente consulta SQL:</p>";
            echo "<pre class='bg-light p-3 rounded'><code>";
            echo "INSERT INTO tab_matriculas (id_estud, id_curso, fecha_matricula) VALUES (" . htmlspecialchars($id_estud) . ", " . htmlspecialchars($id_curso_esperado) . ", CURRENT_DATE);";
            echo "</code></pre>";
            echo "<p><b>Nota:</b> Puede usar una herramienta como pgAdmin o la línea de comandos de PostgreSQL (psql) para ejecutar esta consulta.</p>";
        }
        echo "</div></div>";

    } else {
        echo "<div class='alert alert-warning'><b>Estudiante no encontrado.</b> No se encontró ningún estudiante con el número de documento '" . htmlspecialchars($documento_a_buscar) . "'. Verifique que el número sea correcto.</div>";
        echo "</div></div>";
    }

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'><h4>Error de Conexión a la Base de Datos</h4><p>No se pudo conectar a la base de datos. Verifique la configuración en <code>php/config.php</code>.</p>";
    echo "<p><b>Mensaje de error:</b> " . $e->getMessage() . "</p></div>";
}

echo "</div></body></html>";
?>