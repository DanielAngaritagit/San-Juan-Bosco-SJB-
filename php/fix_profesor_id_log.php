<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$response = ['success' => false, 'message' => '', 'updated_professors' => 0];

try {
    $pdo->beginTransaction();

    // Update id_log in tab_profesores by joining with login table
    $stmt_update = $pdo->prepare("
        UPDATE tab_profesores tp
        SET id_log = l.id_log
        FROM login l
        WHERE tp.id_log IS NULL
          AND tp.email = l.email
          AND l.rol = 'profesor'
    ");
    $stmt_update->execute();
    $response['updated_professors'] = $stmt_update->rowCount();

    $pdo->commit();
    $response['success'] = true;
    $response['message'] = 'Professor id_log values fixed successfully.';

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $response['message'] = 'Database error: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = 'General error: ' . $e->getMessage();
}

echo json_encode($response);
?>