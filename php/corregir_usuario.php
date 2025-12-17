<?php
require_once 'conexion.php';

$usuario_viejo = 'keinerdaielbutistaangarita@gmail.com';
$nuevo_usuario = '2008';

try {
    // Actualizamos directamente en la tabla login
    $sql_actualizar = "UPDATE login SET usuario = :nuevo_usuario WHERE usuario = :usuario_viejo AND rol = 'admin'";
    $stmt_actualizar = $conn->prepare($sql_actualizar);
    $stmt_actualizar->bindParam(':nuevo_usuario', $nuevo_usuario);
    $stmt_actualizar->bindParam(':usuario_viejo', $usuario_viejo);
    
    $stmt_actualizar->execute();

    // Verificamos si alguna fila fue afectada para confirmar el cambio
    if ($stmt_actualizar->rowCount() > 0) {
        echo "¡Éxito! El usuario '" . htmlspecialchars($usuario_viejo) . "' ha sido restaurado a '" . htmlspecialchars($nuevo_usuario) . "'. Ya puedes iniciar sesión.";
    } else {
        echo "Aviso: No se encontró un usuario administrador con el nombre '" . htmlspecialchars($usuario_viejo) . "'. Es posible que el cambio ya se haya realizado o el usuario no exista.";
    }

} catch (PDOException $e) {
    // Manejo de error para el caso en que el nuevo nombre de usuario ya exista
    if ($e->getCode() == '23505') { // Código de violación de unicidad en PostgreSQL
        echo "Error: El nombre de usuario '" . htmlspecialchars($nuevo_usuario) . "' ya existe y está asignado a otra cuenta. No se pueden tener usuarios duplicados.";
    } else {
        die("Error al procesar la solicitud: " . $e->getMessage());
    }
}
?>