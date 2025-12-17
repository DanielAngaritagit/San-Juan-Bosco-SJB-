<?php
// Incluir el archivo de configuración de la base de datos
require_once '../php/config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $documento_a_eliminar = $_POST['no_documento'] ?? '';

    if (empty($documento_a_eliminar)) {
        $response['message'] = 'El número de documento es requerido para la eliminación.';
        echo json_encode($response);
        exit();
    }

    try {
        // Conexión a la base de datos usando PDO
        $pdo = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Iniciar una transacción para asegurar la integridad de los datos
        $pdo->beginTransaction();

        // 1. Eliminar el registro de la tabla 'tab_acudiente'
        $stmt_delete_acudiente = $pdo->prepare("DELETE FROM tab_acudiente WHERE no_documento = :no_documento");
        $stmt_delete_acudiente->execute([':no_documento' => $documento_a_eliminar]);
        $acudiente_deleted_rows = $stmt_delete_acudiente->rowCount();

        // 2. Eliminar el registro de la tabla 'login' (si el documento es el usuario de login)
        $stmt_delete_login = $pdo->prepare("DELETE FROM login WHERE usuario = :no_documento");
        $stmt_delete_login->execute([':no_documento' => $documento_a_eliminar]);
        $login_deleted_rows = $stmt_delete_login->rowCount();

        // Confirmar la transacción si todo fue bien
        $pdo->commit();

        if ($acudiente_deleted_rows > 0) {
            $response['success'] = true;
            $response['message'] = "Acudiente con documento '{$documento_a_eliminar}' eliminado exitosamente. Se eliminaron {$login_deleted_rows} registros de login y {$acudiente_deleted_rows} registros de acudiente.";
        } else {
            $response['message'] = "No se encontró ningún acudiente con el documento '{$documento_a_eliminar}'.";
        }

    } catch (PDOException $e) {
        // Revertir la transacción en caso de error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $response['message'] = 'Error de base de datos al eliminar el acudiente: ' . $e->getMessage();
    } catch (Exception $e) {
        $response['message'] = 'Error inesperado: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Método de solicitud no permitido.';
}

echo json_encode($response);
?>