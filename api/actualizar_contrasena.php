<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

// Incluir la conexión a la base de datos
require_once '../php/conexion.php';

// Obtener el cuerpo de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['token']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

$token = $data['token'];
$new_password = $data['password'];

if (strlen($new_password) < 8) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 8 caracteres.']);
    exit;
}

try {
    // 1. Verificar el token en la base de datos
    $stmt = $conn->prepare(
        "SELECT id_log, fecha_expiracion FROM tab_password_reset WHERE token = :token AND utilizado = FALSE"
    );
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $reset_request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reset_request) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'El token no es válido o ya ha sido utilizado.']);
        exit;
    }

    // 2. Verificar si el token ha expirado
    $fecha_expiracion = new DateTime($reset_request['fecha_expiracion']);
    $ahora = new DateTime();

    if ($ahora > $fecha_expiracion) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'El token ha expirado. Por favor, solicita un nuevo enlace de recuperación.']);
        exit;
    }

    $id_log = $reset_request['id_log'];

    // 3. Hashear la nueva contraseña (¡IMPORTANTE!)
    // Utiliza el mismo algoritmo de hash que usas en el resto de tu aplicación.
    // Si no estás seguro, password_hash con PASSWORD_DEFAULT es la mejor opción.
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Iniciar una transacción para asegurar la integridad de los datos
    $conn->beginTransaction();

    // 4. Actualizar la contraseña en la tabla de login
    $stmt_update = $conn->prepare("UPDATE login SET contrasena = :contrasena WHERE id_log = :id_log");
    $stmt_update->bindParam(':contrasena', $hashed_password);
    $stmt_update->bindParam(':id_log', $id_log);
    $stmt_update->execute();

    // 5. Marcar el token como utilizado
    $stmt_invalidate = $conn->prepare("UPDATE tab_password_reset SET utilizado = TRUE WHERE token = :token");
    $stmt_invalidate->bindParam(':token', $token);
    $stmt_invalidate->execute();

    // Confirmar la transacción
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Contraseña actualizada con éxito.']);

} catch (PDOException $e) {
    // Revertir la transacción en caso de error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(500);
    error_log('Error de base de datos en actualizar_contrasena.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor. No se pudo actualizar la contraseña.']);
} catch (Exception $e) {
    http_response_code(500);
    error_log('Error general en actualizar_contrasena.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Ocurrió un error inesperado.']);
}

?>