<?php
require_once 'conexion.php';

if ($argc < 2) {
    echo "Uso: php delete_users_by_role.php <rol1> [<rol2> ...]\n";
    exit(1);
}

$roles = array_slice($argv, 1);

echo "Eliminando usuarios con los roles: " . implode(', ', $roles) . "...\n";

try {
    $pdo->beginTransaction();

    // 1. Get all id_log for specified roles
    $placeholders = implode(',', array_fill(0, count($roles), '?'));
    $stmt_users = $pdo->prepare("SELECT id_log, usuario, rol FROM login WHERE rol IN ({$placeholders})");
    $stmt_users->execute($roles);
    $users_to_delete = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

    if (empty($users_to_delete)) {
        echo "No se encontraron usuarios con los roles especificados.\n";
        $pdo->commit();
        exit(0);
    }

    $id_logs_to_delete = array_column($users_to_delete, 'id_log');
    $id_logs_placeholders = implode(',', array_fill(0, count($id_logs_to_delete), '?'));

    $student_no_documentos = [];
    $parent_id_logs = [];

    foreach ($users_to_delete as $user) {
        if ($user['rol'] === 'estudiante') {
            $student_no_documentos[] = $user['usuario'];
        } elseif ($user['rol'] === 'padre') {
            $parent_id_logs[] = $user['id_log'];
        }
    }

    // 2. Get all id_acudiente from tab_acudiente where id_log is in the list of 'padre' id_logs
    $acudiente_ids = [];
    if (!empty($parent_id_logs)) {
        $parent_id_logs_placeholders = implode(',', array_fill(0, count($parent_id_logs), '?'));
        $stmt_acudiente_ids = $pdo->prepare("SELECT id_acudiente FROM tab_acudiente WHERE id_log IN ({$parent_id_logs_placeholders})");
        $stmt_acudiente_ids->execute($parent_id_logs);
        $acudiente_ids = $stmt_acudiente_ids->fetchAll(PDO::FETCH_COLUMN);
    }

    // 3. Get all id_ficha from tab_estudiante based on no_documento or id_acudiente
    $ficha_ids_to_delete_from_estudiante_table = [];
    $no_documentos_placeholders = implode(',', array_fill(0, count($student_no_documentos), '?'));
    $acudiente_ids_placeholders = implode(',', array_fill(0, count($acudiente_ids), '?'));

    if (!empty($student_no_documentos) && !empty($acudiente_ids)) {
        $stmt_ficha_ids = $pdo->prepare("SELECT id_ficha FROM tab_estudiante WHERE no_documento IN ({$no_documentos_placeholders}) OR id_acudiente IN ({$acudiente_ids_placeholders})");
        $stmt_ficha_ids->execute(array_merge($student_no_documentos, $acudiente_ids));
        $ficha_ids_to_delete_from_estudiante_table = $stmt_ficha_ids->fetchAll(PDO::FETCH_COLUMN);
    } elseif (!empty($student_no_documentos)) {
        $stmt_ficha_ids = $pdo->prepare("SELECT id_ficha FROM tab_estudiante WHERE no_documento IN ({$no_documentos_placeholders})");
        $stmt_ficha_ids->execute($student_no_documentos);
        $ficha_ids_to_delete_from_estudiante_table = $stmt_ficha_ids->fetchAll(PDO::FETCH_COLUMN);
    } elseif (!empty($acudiente_ids)) {
        $stmt_ficha_ids = $pdo->prepare("SELECT id_ficha FROM tab_estudiante WHERE id_acudiente IN ({$acudiente_ids_placeholders})");
        $stmt_ficha_ids->execute($acudiente_ids);
        $ficha_ids_to_delete_from_estudiante_table = $stmt_ficha_ids->fetchAll(PDO::FETCH_COLUMN);
    }

    // Start Deletion Process (Order matters due to foreign keys)

    // Delete student-related data first
    if (!empty($ficha_ids_to_delete_from_estudiante_table)) {
        $ficha_ids_to_delete_from_estudiante_table_placeholders = implode(',', array_fill(0, count($ficha_ids_to_delete_from_estudiante_table), '?'));

        // 5. Delete from tab_asistencia
        $stmt = $pdo->prepare("DELETE FROM tab_asistencia WHERE id_estud IN ({$ficha_ids_to_delete_from_estudiante_table_placeholders})");
        $stmt->execute($ficha_ids_to_delete_from_estudiante_table);
        echo "Eliminados registros de asistencia (estudiantes).\n";

        // 6. Delete from tab_calificaciones
        $stmt = $pdo->prepare("DELETE FROM tab_calificaciones WHERE id_estud IN ({$ficha_ids_to_delete_from_estudiante_table_placeholders})");
        $stmt->execute($ficha_ids_to_delete_from_estudiante_table);
        echo "Eliminados registros de calificaciones (estudiantes).\n";

        // 7. Delete from tab_comunicaciones
        $stmt = $pdo->prepare("DELETE FROM tab_comunicaciones WHERE id_estud IN ({$ficha_ids_to_delete_from_estudiante_table_placeholders})");
        $stmt->execute($ficha_ids_to_delete_from_estudiante_table);
        echo "Eliminados registros de comunicaciones (estudiantes).\n";

        // 8. Delete from tab_matriculas
        $stmt = $pdo->prepare("DELETE FROM tab_matriculas WHERE id_estud IN ({$ficha_ids_to_delete_from_estudiante_table_placeholders})");
        $stmt->execute($ficha_ids_to_delete_from_estudiante_table);
        echo "Eliminados registros de matriculas (estudiantes).\n";

        // 9. Delete from tab_estudiante
        $stmt = $pdo->prepare("DELETE FROM tab_estudiante WHERE id_ficha IN ({$ficha_ids_to_delete_from_estudiante_table_placeholders})");
        $stmt->execute($ficha_ids_to_delete_from_estudiante_table);
        echo "Eliminados registros de ficha de estudiante.\n";
    }

    // Delete general user-related data (profesores, seguridad, etc.)
    // These tables reference login.id_log directly or indirectly via tab_usuarios

    // 11. Delete from tab_acudiente (Moved here to ensure deletion before login)
    $stmt = $pdo->prepare("DELETE FROM tab_acudiente WHERE id_log IN ({$id_logs_placeholders})");
    $stmt->execute($id_logs_to_delete);
    echo "Eliminados registros de acudiente.\n";

    // 12. Delete from tab_profesores
    $stmt = $pdo->prepare("DELETE FROM tab_profesores WHERE id_log IN ({$id_logs_placeholders})");
    $stmt->execute($id_logs_to_delete);
    echo "Eliminados registros de profesores.\n";

    // 13. Delete from tab_seguridad_respuestas
    $stmt = $pdo->prepare("DELETE FROM tab_seguridad_respuestas WHERE id_log IN ({$id_logs_placeholders})");
    $stmt->execute($id_logs_to_delete);
    echo "Eliminados registros de seguridad.\n";

    // 14. Delete from tab_password_reset
    $stmt = $pdo->prepare("DELETE FROM tab_password_reset WHERE id_log IN ({$id_logs_placeholders})");
    $stmt->execute($id_logs_to_delete);
    echo "Eliminados registros de reseteo de contraseña.\n";

    // 15. Delete from accesos
    $stmt = $pdo->prepare("DELETE FROM accesos WHERE usuario_id IN ({$id_logs_placeholders})");
    $stmt->execute($id_logs_to_delete);
    echo "Eliminados registros de accesos.\n";

    // 16. Delete from tab_usuarios
    $stmt = $pdo->prepare("DELETE FROM tab_usuarios WHERE id_log IN ({$id_logs_placeholders})");
    $stmt->execute($id_logs_to_delete);
    echo "Eliminados registros de usuarios.\n";

    // 17. Delete from login
    $stmt = $pdo->prepare("DELETE FROM login WHERE id_log IN ({$id_logs_placeholders})");
    $stmt->execute($id_logs_to_delete);
    echo "Eliminados registros de login.\n";

    $pdo->commit();

    echo "Usuarios eliminados exitosamente.\n";

} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Error al eliminar usuarios: " . $e->getMessage() . "\n";
    exit(1);
}