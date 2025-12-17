<?php
header('Content-Type: application/json');

require_once 'config.php';

// Configuración de conexión
$config = [
    'host' => DB_HOST,
    'dbname' => DB_NAME,
    'user' => DB_USER,
    'pass' => DB_PASS,
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];

try {
    // Establecer conexión
    $dsn = "pgsql:host={$config['host']};dbname={$config['dbname']}";
    $pdo = new PDO($dsn, $config['user'], $config['pass'], $config['options']);
    
    $response_data = [];
    $request_type = $_GET['type'] ?? 'general';

    switch ($request_type) {
        case 'general':
            // Consultar la función PostgreSQL para estadísticas generales
            $stmt = $pdo->query("SELECT * FROM estadisticas_usuarios()");
            $estadisticas = $stmt->fetch();
            $response_data = ['success' => true, 'data' => $estadisticas];
            break;
        case 'profesor_rendimiento':
            // Datos de ejemplo para el rendimiento del profesor
            // En un escenario real, aquí iría la lógica para consultar la BD
            $response_data = [
                'success' => true,
                'labels' => ['Aprobados', 'Reprobados'],
                'values' => [85, 15] // Ejemplo: 85% aprobados, 15% reprobados
            ];
            break;
        default:
            $response_data = ['success' => false, 'message' => 'Tipo de solicitud no reconocido.'];
            break;
    }
    
    // Configurar caché (1 hora)
    header('Cache-Control: public, max-age=3600');
    
    echo json_encode($response_data);

} catch (PDOException $e) {
    // Registrar error (solo para desarrollo, en producción usa un sistema de logging)
    error_log('Error en estadisticas.php: ' . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener estadísticas',
        'error' => $e->getMessage() // Solo para desarrollo, quitar en producción
    ]);
}
?>