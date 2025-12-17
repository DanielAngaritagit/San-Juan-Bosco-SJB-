<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$response = ['success' => false, 'message' => '', 'columns' => []];

if (!isset($_GET['table'])) {
    $response['message'] = 'Error: El parámetro "table" es requerido. Ej: get_table_schema.php?table=nombre_de_la_tabla';
    echo json_encode($response);
    exit;
}

$tableName = $_GET['table'];

// Basic validation to prevent obvious SQL injection issues, although it's a table name.
if (!preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
    $response['message'] = 'Error: Nombre de tabla no válido.';
    echo json_encode($response);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT column_name, data_type, is_nullable
        FROM information_schema.columns
        WHERE table_schema = 'public' AND table_name = :table_name
        ORDER BY ordinal_position;
    ");
    $stmt->bindParam(':table_name', $tableName, PDO::PARAM_STR);
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($columns)) {
        $response['message'] = 'No se encontró la tabla o no tiene columnas: ' . htmlspecialchars($tableName);
    } else {
        $response['success'] = true;
        $response['message'] = 'Schema for ' . htmlspecialchars($tableName) . ' fetched successfully.';
        $response['columns'] = $columns;
    }

} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = 'General error: ' . $e->getMessage();
}

echo json_encode($response);
?>