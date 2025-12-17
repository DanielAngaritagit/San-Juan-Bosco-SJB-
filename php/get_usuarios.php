<?php
header('Content-Type: application/json');

require_once 'conexion.php';

// Obtener el rol de la query string (ej. /get_usuarios.php?rol=profesor)
$rol = isset($_GET['rol']) ? $_GET['rol'] : '';

if (empty($rol)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'El rol es requerido']);
    exit;
}

try {
    // Consultar usuarios por rol
    // Se asume que la tabla de login tiene las columnas 'id_log', 'nombre', 'apellido' y 'rol'
    $stmt = $pdo->prepare("SELECT id_log, usuario AS nombre, '' AS apellido FROM login WHERE rol = :rol ORDER BY nombre");
    $stmt->execute([':rol' => $rol]);

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($usuarios);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
    exit;
}
?>