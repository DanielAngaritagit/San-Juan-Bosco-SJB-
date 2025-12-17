<?php
session_start();
header('Content-Type: application/json');

require_once 'conexion.php';

$response = ['success' => false, 'name' => '', 'role' => '', 'profile_pic' => '../multimedia/administrador/user_default.png'];

if (!isset($_SESSION['id_log']) || !isset($_SESSION['rol'])) {
    echo json_encode($response);
    exit;
}

$id_log = $_SESSION['id_log'];
$rol = $_SESSION['rol'];

try {
    $response['role'] = $rol;

    switch ($rol) {
        case 'admin':
        case 'administrativo':
            $stmt = $pdo->prepare("SELECT l.usuario, l.foto_url FROM login l WHERE l.id_log = :id_log");
            $stmt->execute(['id_log' => $id_log]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $response['name'] = $user['usuario'];
                $profile_pic_url = $user['foto_url'];
                if ($profile_pic_url && strpos($profile_pic_url, '/') !== 0 && strpos($profile_pic_url, 'http') !== 0) {
                    $profile_pic_url = '/sjb/' . $profile_pic_url;
                }
                $response['profile_pic'] = $profile_pic_url ? $profile_pic_url : '/sjb/multimedia/administrador/user_default.png';
            }
            break;
        case 'profesor':
            $stmt = $pdo->prepare("SELECT p.nombres, p.apellidos, l.foto_url FROM tab_profesores p JOIN login l ON l.id_log = p.id_profesor WHERE l.id_log = :id_log");
            $stmt->execute(['id_log' => $id_log]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $response['name'] = $user['nombres'] . ' ' . $user['apellidos'];
                $profile_pic_url = $user['foto_url'];
                if ($profile_pic_url && strpos($profile_pic_url, '/') !== 0 && strpos($profile_pic_url, 'http') !== 0) {
                    $profile_pic_url = '/sjb/' . $profile_pic_url;
                }
                $response['profile_pic'] = $profile_pic_url ? $profile_pic_url : '/sjb/multimedia/administrador/user_default.png';
            }
            break;
        case 'estudiante':
            $stmt = $pdo->prepare("SELECT te.nombres, te.apellido1, te.apellido2, l.foto_url FROM login l JOIN tab_estudiante te ON l.usuario = te.no_documento WHERE l.id_log = :id_log");
            $stmt->execute(['id_log' => $id_log]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $response['name'] = $user['nombres'] . ' ' . $user['apellido1'] . (isset($user['apellido2']) ? ' ' . $user['apellido2'] : '');
                $profile_pic_url = $user['foto_url'];
                if ($profile_pic_url && strpos($profile_pic_url, '/') !== 0 && strpos($profile_pic_url, 'http') !== 0) {
                    $profile_pic_url = '/sjb/' . $profile_pic_url;
                }
                $response['profile_pic'] = $profile_pic_url ? $profile_pic_url : '/sjb/multimedia/administrador/user_default.png';
            }
            break;
        case 'padre':
            $stmt = $pdo->prepare("SELECT ta.nombres, ta.apellidos, l.foto_url FROM login l JOIN tab_acudiente ta ON l.usuario = ta.no_documento WHERE l.id_log = :id_log");
            $stmt->execute(['id_log' => $id_log]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $response['name'] = $user['nombres'] . ' ' . $user['apellidos'];
                $profile_pic_url = $user['foto_url'];
                if ($profile_pic_url && strpos($profile_pic_url, '/') !== 0 && strpos($profile_pic_url, 'http') !== 0) {
                    $profile_pic_url = '/sjb/' . $profile_pic_url;
                }
                $response['profile_pic'] = $profile_pic_url ? $profile_pic_url : '/sjb/multimedia/administrador/user_default.png';
            }
            break;
    }

    $response['success'] = true;

} catch (PDOException $e) {
    $response['error'] = 'Error de base de datos: ' . $e->getMessage();
} catch (Exception $e) {
    $response['error'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>