<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
session_start();
header('Content-Type: application/json');

require_once '../php/conexion.php';

if (!isset($_SESSION['id_log']) || !isset($_SESSION['rol'])) {
    echo json_encode(['success' => false, 'message' => 'No has iniciado sesión o tu rol no está definido.']);
    exit;
}

$id_log = $_SESSION['id_log'];
$rol = $_SESSION['rol'];

$primer_nombre = $_POST['primer_nombre'] ?? '';
$segundo_nombre = $_POST['segundo_nombre'] ?? '';
$primer_apellido = $_POST['primer_apellido'] ?? '';
$segundo_apellido = $_POST['segundo_apellido'] ?? '';

$nombres = trim($primer_nombre . ' ' . $segundo_nombre);
$apellidos = trim($primer_apellido . ' ' . $segundo_apellido);

$email = $_POST['email'] ?? null;
$telefono = $_POST['telefono'] ?? null;
$direccion = $_POST['direccion'] ?? null;
$rh = $_POST['rh'] ?? null;
$alergias = $_POST['alergias'] ?? null;

try {
    $pdo->beginTransaction();

    // 1. Update the central login table
    $stmt_login = $pdo->prepare("UPDATE login SET email = :email WHERE id_log = :id_log");
    $stmt_login->execute([':email' => $email, ':id_log' => $id_log]);

    // 2. Update the role-specific table
    $table = '';
    $fields = [];
    $params = [];

    switch ($rol) {
        case 'admin':
            $table = 'tab_administradores';
            $fields = ['nombres', 'apellidos', 'telefono', 'direccion', 'rh', 'alergias'];
            $params = [
                ':nombres' => $nombres,
                ':apellidos' => $apellidos,
                ':telefono' => $telefono,
                ':direccion' => $direccion,
                ':rh' => $rh,
                ':alergias' => $alergias,
                ':id_log' => $id_log
            ];
            break;
        case 'profesor':
            $table = 'tab_profesores';
            $fields = ['nombres', 'apellidos', 'telefono', 'direccion', 'rh', 'alergias'];
            $params = [
                ':nombres' => $nombres,
                ':apellidos' => $apellidos,
                ':telefono' => $telefono,
                ':direccion' => $direccion,
                ':rh' => $rh,
                ':alergias' => $alergias,
                ':id_log' => $id_log
            ];
            break;
        case 'padre':
            $table = 'tab_acudiente';
            $fields = ['nombres', 'apellidos', 'telefono', 'direccionp', 'rh', 'alergias'];
            $params = [
                ':nombres' => $nombres,
                ':apellidos' => $apellidos,
                ':telefono' => $telefono,
                ':direccionp' => $direccion, // Note: mapping 'direccion' from form to 'direccionp'
                ':rh' => $rh,
                ':alergias' => $alergias,
                ':id_log' => $id_log
            ];
            break;
        case 'estudiante':
            // Get the student's document number from the login table
            $stmt_get_doc = $pdo->prepare("SELECT usuario FROM login WHERE id_log = :id_log");
            $stmt_get_doc->execute([':id_log' => $id_log]);
            $student_no_documento = $stmt_get_doc->fetchColumn();

            if (!$student_no_documento) {
                throw new Exception("No se encontró el número de documento del estudiante asociado al login.");
            }

            $update_query = "UPDATE tab_estudiante SET nombres = :nombres, apellido1 = :apellido1, apellido2 = :apellido2, telefonos = :telefonos, direccion = :direccion, rh = :rh, alergias = :alergias WHERE no_documento = :no_documento";
            $params = [
                ':nombres' => $primer_nombre,
                ':apellido1' => $primer_apellido,
                ':apellido2' => $segundo_apellido,
                ':telefonos' => $telefono,
                ':direccion' => $direccion,
                ':rh' => $rh,
                ':alergias' => $alergias,
                ':no_documento' => $student_no_documento
            ];
            $stmt = $pdo->prepare($update_query);
            $stmt->execute($params);
            $table = ''; // Prevent generic query from running
            break;
    }

    if ($table) {
        $update_fields = array_map(function($field) {
            return "$field = :$field";
        }, $fields);
        $update_query = "UPDATE $table SET " . implode(', ', $update_fields) . " WHERE id_log = :id_log";
        
        $stmt = $pdo->prepare($update_query);
        $stmt->execute($params);
    }

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Perfil actualizado correctamente.']);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Update profile error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Ocurrió un error al actualizar el perfil: ' . $e->getMessage()]);
}
?>