<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['role'])) {
    header('Location: ../acceso_anonimo.html?error=rol_no_especificado');
    exit();
}

$role = $_GET['role'];
$role_map = [
    'administrador' => 'admin',
    'profesor' => 'profesor',
    'estudiante' => 'estudiante',
    'padre' => 'padre'
];

// Mapeo de roles de la URL a los roles de la base de datos
$db_role = '';
switch ($role) {
    case 'administrador':
        $db_role = 'admin';
        break;
    case 'profesor':
        $db_role = 'profesor';
        break;
    case 'estudiante':
        $db_role = 'estudiante';
        break;
    case 'padre':
        $db_role = 'padre';
        break;
    default:
        header('Location: ../acceso_anonimo.html?error=rol_invalido');
        exit();
}


$conn = conectar();

try {
    $sql = "SELECT id, usuario FROM login WHERE rol = :rol ORDER BY id LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':rol', $db_role);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Limpiar cualquier sesión antigua
        session_unset();
        session_destroy();
        session_start();

        // Configurar la nueva sesión de invitado
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['usuario'];
        $_SESSION['user_role'] = $db_role;
        $_SESSION['is_guest'] = true; // Marcar como sesión de invitado

        // Redirigir al dashboard correspondiente
        $redirect_path = '';
        switch ($db_role) {
            case 'admin':
                $redirect_path = '../admin/admin.php';
                break;
            case 'profesor':
                $redirect_path = '../profesor/profesor.php';
                break;
            case 'estudiante':
                $redirect_path = '../estudiante/estudiante.php';
                break;
            case 'padre':
                $redirect_path = '../padre/padre.php';
                break;
        }

        header("Location: " . $redirect_path);
        exit();

    } else {
        header('Location: ../acceso_anonimo.html?error=no_user_found&role=' . $role);
        exit();
    }

} catch (PDOException $e) {
    // En un entorno de producción, registrar el error en lugar de mostrarlo
    header('Location: ../acceso_anonimo.html?error=db_error');
    exit();
} finally {
    $conn = null;
}
?>