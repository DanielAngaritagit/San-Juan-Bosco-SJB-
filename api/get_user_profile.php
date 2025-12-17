<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
session_start();

header('Content-Type: application/json');

// Headers para prevenir el cacheo
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once '../php/conexion.php';

if (!isset($_SESSION['id_log'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'No has iniciado sesión (id_log no encontrado en la sesión).',
        'session_data' => $_SESSION // DEBUGGING: Muestra el contenido de la sesión
    ]);
    exit;
}

$id_log = $_SESSION['id_log'];
$rol = $_SESSION['rol'];

try {
    $query = '';
    $baseQuery = "SELECT l.usuario, l.rol, l.foto_url, l.email";

    switch ($rol) {
        case 'admin':
            $query = "$baseQuery, 
                         a.nombres, a.apellidos, a.tipo_documento, a.no_documento, 
                         a.fecha_nacimiento, a.telefono, a.direccion, a.fecha_expedicion, a.cargo, a.eps, a.rh, a.alergias, a.sexo
                      FROM login l
                      LEFT JOIN tab_administradores a ON l.id_log = a.id_log
                      WHERE l.id_log = :id_log";
            break;

        case 'profesor':
            $query = "$baseQuery, 
                         p.nombres, p.apellidos, p.tipo_documento, p.no_documento, 
                         p.fecha_nacimiento, p.telefono, p.direccion, p.especialidad, 
                         p.titulo_academico, p.rh, p.alergias, p.fecha_expedicion, p.nacionalidad, p.eps
                      FROM login l
                      LEFT JOIN tab_profesores p ON l.id_log = p.id_log
                      WHERE l.id_log = :id_log";
            break;

        case 'estudiante':
            $query = "SELECT 
                         l.usuario, l.rol, l.foto_url, l.email,
                         e.nombres, e.apellido1 as apellidos, e.tipo_documento, e.no_documento,
                         e.fecha_nacimiento, e.sexo, e.rh, e.direccion, e.telefonos as telefono, 
                         e.alergias, e.fecha_expedicion
                      FROM login l
                      LEFT JOIN tab_estudiante e ON l.usuario = e.no_documento
                      WHERE l.id_log = :id_log";
            break;

        case 'padre':
            $query = "$baseQuery, 
                         ac.nombres, ac.apellidos, ac.tipo_documento, ac.no_documento, 
                         ac.fecha_nacimiento, ac.sexo, ac.rh, ac.telefono, 
                         ac.direccionp as direccion, ac.estado_civil, ac.parentesco, ac.alergias
                      FROM login l
                      LEFT JOIN tab_acudiente ac ON l.id_log = ac.id_log
                      WHERE l.id_log = :id_log";
            break;
    }

    if (empty($query)) {
        throw new Exception('Rol de usuario no válido o no soportado para perfiles.');
    }

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_log', $id_log, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode(['success' => true, 'data' => $user]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'No se encontró un perfil detallado para el usuario con id_log: ' . $id_log . ' y rol: ' . $rol,
            'session_data' => $_SESSION
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error en la base de datos o en la lógica del script: ' . $e->getMessage(),
        'session_data' => $_SESSION
    ]);
}
?>