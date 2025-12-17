<?php
/**
 * Script de migración único para consolidar usuarios con rol 'administrativo' a 'admin'.
 *
 * - Encuentra todos los usuarios con el rol 'administrativo'.
 * - Para cada usuario, verifica si ya existe un perfil en `tab_administradores`.
 * - Si no existe un perfil, crea uno de marcador de posición utilizando la información de la tabla `login`.
 * - Actualiza el rol del usuario a 'admin' en la tabla `login`.
 */

header('Content-Type: text/plain');

require_once 'conexion.php';

echo "Iniciando migración de roles 'administrativo' a 'admin'...

";

try {
    $pdo->beginTransaction();

    // 1. Encontrar todos los usuarios con el rol 'administrativo'
    $stmt_find = $pdo->query("SELECT id_log, usuario, email FROM login WHERE rol = 'administrativo'");
    $administrativos = $stmt_find->fetchAll(PDO::FETCH_ASSOC);

    if (empty($administrativos)) {
        echo "No se encontraron usuarios con el rol 'administrativo'. No se necesita migración.
";
        $pdo->commit();
        exit;
    }

    echo "Se encontraron " . count($administrativos) . " usuarios para migrar.

";

    $update_role_stmt = $pdo->prepare("UPDATE login SET rol = 'admin' WHERE id_log = :id_log");
    $check_admin_stmt = $pdo->prepare("SELECT COUNT(*) FROM tab_administradores WHERE id_log = :id_log");
    $create_admin_stmt = $pdo->prepare(
        "INSERT INTO tab_administradores (id_log, nombres, apellidos, no_documento, email, cargo) VALUES (:id_log, :nombres, :apellidos, :no_documento, :email, 'Administrativo Migrado')"
    );

    foreach ($administrativos as $user) {
        $id_log = $user['id_log'];
        echo "Procesando usuario id_log: $id_log (usuario: {$user['usuario']})
";

        // 2. Verificar si ya existe un perfil en tab_administradores
        $check_admin_stmt->execute([':id_log' => $id_log]);
        $profile_exists = $check_admin_stmt->fetchColumn() > 0;

        if ($profile_exists) {
            echo "  - El perfil en tab_administradores ya existe. Omitiendo la creación del perfil.
";
        } else {
            // 3. Si no existe, crear un perfil de marcador de posición
            echo "  - No se encontró perfil en tab_administradores. Creando perfil de marcador de posición...
";
            $create_admin_stmt->execute([
                ':id_log' => $id_log,
                ':nombres' => $user['usuario'], // Usar el nombre de usuario como nombre
                ':apellidos' => '(Migrado)',      // Apellido de marcador de posición
                ':no_documento' => $user['usuario'],// Usar el nombre de usuario como no_documento
                ':email' => $user['email'] ?? 'no-email@migrado.com'
            ]);
            echo "  - Perfil de marcador de posición creado.
";
        }

        // 4. Actualizar el rol en la tabla de login
        $update_role_stmt->execute([':id_log' => $id_log]);
        echo "  - Rol actualizado a 'admin' en la tabla de login.

";
    }

    $pdo->commit();
    echo "¡Migración completada exitosamente!
";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "ERROR: Ocurrió una excepción durante la migración.
";
    echo "Mensaje: " . $e->getMessage() . "
";
    http_response_code(500);
}
?>
