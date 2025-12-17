<?php
// delete_all_users.php

// !!! ADVERTENCIA: Este script eliminará TODOS los datos de usuario de la base de datos.
// !!! Es una operación IRREVERSIBLE. Úselo con EXTREMA PRECAUCIÓN.

// Incluir el archivo de conexión a la base de datos
require_once 'conexion.php';

header('Content-Type: application/json');

// --- MEDIDA DE SEGURIDAD: Requiere un parámetro específico para ejecutarse ---
// Esto evita que el script se ejecute accidentalmente al acceder a la URL directamente.
// En un entorno de producción, se debería implementar una autenticación de super-administrador.
if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'true_delete_all_users') {
    echo json_encode([
        'success' => false,
        'message' => 'Operación no autorizada. Para eliminar todos los usuarios, debe pasar el parámetro `?confirm=true_delete_all_users` en la URL. ¡ADVERTENCIA: ESTO ES IRREVERSIBLE!'
    ]);
    exit();
}

try {
    $pdo->beginTransaction();

    // Desactivar temporalmente las comprobaciones de claves foráneas para evitar problemas de orden de eliminación
    // Esto es útil si la estructura de la base de datos no tiene ON DELETE CASCADE en todas partes
    $pdo->exec('SET session_replication_role = replica;'); // Para PostgreSQL

    // Eliminar datos de tablas dependientes primero (tablas de estudiantes)
    $pdo->exec("DELETE FROM tab_calificaciones;");
    $pdo->exec("DELETE FROM tab_asistencia;");
    $pdo->exec("DELETE FROM tab_matriculas;");
    $pdo->exec("DELETE FROM tab_estudiante;"); // This table does exist

    // Eliminar de las tablas de roles principales
    $pdo->exec("DELETE FROM tab_acudiente;");
    $pdo->exec("DELETE FROM tab_profesores;");
    $pdo->exec("DELETE FROM tab_administradores;");

    // Finalmente, eliminar de la tabla de login
    $pdo->exec("DELETE FROM login;");

    // Reactivar las comprobaciones de claves foráneas
    $pdo->exec('SET session_replication_role = origin;'); // Para PostgreSQL

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Todos los usuarios y sus datos asociados han sido eliminados exitosamente.'
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    // Reactivar las comprobaciones de claves foráneas en caso de error
    $pdo->exec('SET session_replication_role = origin;'); // Para PostgreSQL
    error_log("Error al eliminar todos los usuarios: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Ocurrió un error al eliminar los usuarios: ' . $e->getMessage()
    ]);
}
?>