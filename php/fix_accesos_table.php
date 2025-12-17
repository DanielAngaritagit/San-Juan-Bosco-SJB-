<?php
require_once 'conexion.php';

try {
    // Verificar si la columna 'detalles' ya existe
    $stmt = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'accesos' AND column_name = 'detalles'");
    $column_exists = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($column_exists) {
        echo "La columna 'detalles' ya existe en la tabla 'accesos'. No se necesita ninguna acción.";
    } else {
        // Añadir la columna 'detalles' a la tabla 'accesos'
        $pdo->exec("ALTER TABLE accesos ADD COLUMN detalles JSON");
        echo "La columna 'detalles' ha sido añadida exitosamente a la tabla 'accesos'.";
    }

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>