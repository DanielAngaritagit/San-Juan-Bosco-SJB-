<?php
require_once 'conexion.php';

// --- LÓGICA DE BÚSQUEDA ---
$search_term = $_GET['search_term'] ?? null;

// --- LÓGICA DE ELIMINACIÓN ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_usuario'])) {
    // ... (código de eliminación sin cambios) ...
}

// --- LÓGICA DE VISUALIZACIÓN ---
function display_records($pdo, $table, $id_column, $search_term = null) {
    $search_column = '';
    if ($table === 'login') $search_column = 'usuario';
    if ($table === 'tab_acudiente') $search_column = 'no_documento';
    if ($table === 'tab_estudiante') $search_column = 'no_documento';

    if ($search_term && $search_column) {
        echo "<h2>Resultados de la búsqueda para '" . htmlspecialchars($search_term) . "' en: {$table}</h2>";
        $sql = "SELECT * FROM {$table} WHERE {$search_column} = ? ORDER BY {$id_column} DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$search_term]);
    } else {
        echo "<h2>Últimos 5 registros en la tabla: {$table}</h2>";
        $sql = "SELECT * FROM {$table} ORDER BY {$id_column} DESC LIMIT 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        echo "<table border='1' style='width:100%; border-collapse: collapse;'>";
        // ... (código de la tabla sin cambios) ...
        echo "</table>";
    } else {
        echo "<p>No se encontraron registros.</p>";
    }
    echo "<hr>";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificación de Datos</title>
    <style> body { font-family: sans-serif; } </style>
</head>
<body>
    <h1>Verificación y Búsqueda de Datos Guardados</h1>

    <!-- Formulario de Búsqueda -->
    <form method="GET" action="">
        <input type="text" name="search_term" placeholder="Buscar por No. de Documento..." value="<?= htmlspecialchars($search_term ?? '') ?>">
        <button type="submit">Buscar</button>
        <a href="verificar_guardado.php">Limpiar Búsqueda</a>
    </form>
    <hr>

    <?php
    // Verificar las tablas
    display_records($pdo, 'tab_acudiente', 'id_acudiente', $search_term);
    display_records($pdo, 'tab_profesores', 'id_profesor', $search_term);
    display_records($pdo, 'tab_estudiante', 'id_ficha', $search_term);
    display_records($pdo, 'login', 'id_log', $search_term);
    ?>
</body>
</html>