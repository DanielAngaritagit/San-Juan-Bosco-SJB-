<?php
header('Content-Type: application/json');

include_once __DIR__ . '/conexion.php'; // Include your database connection

$response = ['success' => false, 'message' => ''];

try {
    $pdo->beginTransaction();

    // Array of individual SQL DELETE statements
    $sql_statements = [
        // 0. Eliminar registros de calificaciones asociados a los profesores
        "DELETE FROM tab_calificaciones
        WHERE id_profesor IN (
            SELECT id_profesor
            FROM tab_profesores
            WHERE id_log IN (SELECT id_log FROM login WHERE rol = 'profesor')
        )",

        // 1. Actualizar profesor_lider_id en tab_grados a NULL para los profesores a eliminar
        "UPDATE tab_grados
        SET profesor_lider_id = NULL
        WHERE profesor_lider_id IN (
            SELECT id_profesor
            FROM tab_profesores
            WHERE id_log IN (SELECT id_log FROM login WHERE rol = 'profesor')
        )",

        // 2. Eliminar las asignaciones de los profesores
        "DELETE FROM tab_profesor_curso tpc
        WHERE EXISTS (
            SELECT 1
            FROM tab_profesores tp
            WHERE tp.id_profesor = tpc.id_profesor
            AND tp.id_log IN (SELECT id_log FROM login WHERE rol = 'profesor')
        )",

        // 3. Eliminar registros de PQRSF asociados a los profesores
        "DELETE FROM tab_pqrsf
        WHERE usuario_id IN (SELECT id_log FROM login WHERE rol = 'profesor')",

        // 4. Eliminar los detalles específicos de los profesores
        "DELETE FROM tab_profesores
        WHERE id_log IN (SELECT id_log FROM login WHERE rol = 'profesor')",

        // 5. Eliminar los detalles generales de usuario para los profesores
        "DELETE FROM tab_usuarios
        WHERE id_log IN (SELECT id_log FROM login WHERE rol = 'profesor')",

        // 6. Eliminar las credenciales de inicio de sesión de los profesores
        "DELETE FROM login
        WHERE rol = 'profesor'"
    ];

    foreach ($sql_statements as $sql) {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }

    $pdo->commit();
    $response['success'] = true;
    $response['message'] = 'Todos los profesores y sus datos asociados han sido eliminados exitosamente.';

} catch (PDOException $e) {
    $pdo->rollBack();
    $response['message'] = 'Error al eliminar profesores: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = 'Error inesperado: ' . $e->getMessage();
}

echo json_encode($response);
?>