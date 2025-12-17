<?php
session_start();
require_once 'conexion.php';

header('Content-Type: application/json');

try {
    // Validar método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }

    // Obtener datos del cuerpo JSON
    $data = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Datos JSON inválidos', 400);
    }

    // Validar campos requeridos
    $required = ['usuario', 'contraseña', 'rol'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("El campo $field es requerido", 400);
        }
    }

    // Verificar si el usuario ya existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM login WHERE usuario = :usuario");
    $stmt->bindParam(':usuario', $data['usuario'], PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->fetchColumn() > 0) {
        throw new Exception('El usuario ya existe', 409);
    }

    // Validar rol permitido
    $rolesPermitidos = ['admin', 'profesor', 'estudiante', 'padre', 'administrativo'];
    if (!in_array(strtolower($data['rol']), $rolesPermitidos)) {
        throw new Exception('Rol no válido', 400);
    }

    // Reset the sequence for the login table to prevent duplicate key errors
    $pdo->exec("SELECT setval('login_id_log_seq', (SELECT COALESCE(MAX(id_log), 1) FROM login), false);");

    // Hash de la contraseña
    $contrasenaHash = password_hash($data['contraseña'], PASSWORD_BCRYPT);

    // Insertar nuevo usuario
    $stmt = $pdo->prepare("INSERT INTO login (usuario, contrasena, rol) VALUES (:usuario, :contrasena, :rol)");
    $stmt->bindParam(':usuario', $data['usuario'], PDO::PARAM_STR);
    $stmt->bindParam(':contrasena', $contrasenaHash, PDO::PARAM_STR);
    $stmt->bindParam(':rol', $data['rol'], PDO::PARAM_STR);
    $stmt->execute();

    // Obtener ID del nuevo usuario
    $usuarioId = $pdo->lastInsertId();

    // Registrar el acceso inicial
    $stmt = $pdo->prepare("INSERT INTO accesos (usuario_id, direccion_ip, agente_usuario, tipo_acceso) 
                          VALUES (:usuario_id, :ip, :user_agent, 'registro')");
    $stmt->execute([
        ':usuario_id' => $usuarioId,
        ':ip' => $_SERVER['REMOTE_ADDR'],
        ':user_agent' => $_SERVER['HTTP_USER_AGENT']
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Usuario registrado exitosamente',
        'usuario_id' => $usuarioId
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
