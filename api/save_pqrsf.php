<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['success' => false, 'message' => 'Error desconocido.'];

// Directorio de subida
$upload_dir = __DIR__ . '/../uploads/';

try {
    if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin' && empty($_POST['id_pqrsf'])) {
        throw new Exception('Los administradores no tienen permitido crear nuevas PQRSF.');
    }

    $pdo = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

    $id_usuario_logueado = $_SESSION['id_log'] ?? null; // ID del usuario logueado

    // Los datos ahora vienen de $_POST porque usamos FormData
    $id_pqrsf = $_POST['id_pqrsf'] ?? null;
    $tipo = $_POST['tipo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $nombre_solicitante = $_POST['nombre_solicitante'] ?? '';
    $contacto_solicitante = $_POST['contacto_solicitante'] ?? '';
    $pqrsf_about_category = $_POST['pqrsf_about_category'] ?? '';
    $pqrsf_about_id = $_POST['pqrsf_about_id'] ?? null; // ID del destinatario específico (profesor/estudiante)
    $estado = $_POST['estado'] ?? 'Pendiente'; // Solo se usa en actualización por admin

    $archivo_adjunto_path = null;

    if (empty($tipo) || empty($descripcion) || empty($nombre_solicitante) || empty($contacto_solicitante) || empty($pqrsf_about_category) || empty($id_usuario_logueado)) {
        throw new Exception('Por favor, complete todos los campos obligatorios y asegúrese de haber iniciado sesión.');
    }

    // Manejo del archivo adjunto
    if (isset($_FILES['archivo_adjunto']) && $_FILES['archivo_adjunto']['error'] == UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['archivo_adjunto']['tmp_name'];
        $file_name = basename($_FILES['archivo_adjunto']['name']);
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = uniqid() . '.' . $file_ext;
        $dest_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($file_tmp_path, $dest_path)) {
            $archivo_adjunto_path = $new_file_name;
        } else {
            throw new Exception('Error al mover el archivo subido.');
        }
    }

    if ($id_pqrsf) {
        // Actualizar PQRSF existente
        $sql = "UPDATE tab_pqrsf SET tipo = :tipo, descripcion = :descripcion, nombre_solicitante = :nombre_solicitante, contacto_solicitante = :contacto_solicitante, destinatario = :destinatario, usuario_id = :usuario_id";
        if ($archivo_adjunto_path) {
            $sql .= ", archivo_adjunto = :archivo_adjunto";
        }
        // Solo un admin puede cambiar el estado.
        if ($_SESSION['rol'] === 'admin') {
            $sql .= ", estado = :estado";
        }
        $sql .= " WHERE id_pqrsf = :id_pqrsf";

        $stmt = $pdo->prepare($sql);
        $params = [
            ':tipo' => $tipo,
            ':descripcion' => $descripcion,
            ':nombre_solicitante' => $nombre_solicitante,
            ':contacto_solicitante' => $contacto_solicitante,
            ':destinatario' => $pqrsf_about_category,
            ':usuario_id' => $pqrsf_about_id, // ID del destinatario
            ':id_pqrsf' => $id_pqrsf
        ];

        if ($_SESSION['rol'] === 'admin') {
            $params[':estado'] = $estado;
        }
        if ($archivo_adjunto_path) {
            $params[':archivo_adjunto'] = $archivo_adjunto_path;
        }

        $stmt->execute($params);
        $response = ['success' => true, 'message' => 'PQRSF actualizada exitosamente.'];

    } else {
        // Insertar nueva PQRSF
        $sql = "INSERT INTO tab_pqrsf (tipo, descripcion, nombre_solicitante, contacto_solicitante, destinatario, usuario_id, estado, archivo_adjunto) VALUES (:tipo, :descripcion, :nombre_solicitante, :contacto_solicitante, :destinatario, :usuario_id, :estado, :archivo_adjunto)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':tipo' => $tipo,
            ':descripcion' => $descripcion,
            ':nombre_solicitante' => $nombre_solicitante,
            ':contacto_solicitante' => $contacto_solicitante,
            ':destinatario' => $pqrsf_about_category,
            ':usuario_id' => $id_usuario_logueado, // El creador de la PQRSF es el usuario logueado
            ':estado' => 'Pendiente', // El estado inicial siempre es pendiente
            ':archivo_adjunto' => $archivo_adjunto_path
        ]);
        $response = ['success' => true, 'message' => 'PQRSF registrada exitosamente.'];
    }

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>