<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

session_start();

require_once 'conexion.php';

if (!isset($_SESSION['id_log'])) {
    echo json_encode(['ok' => false, 'error' => 'No ha iniciado sesión']);
    exit;
}

$id_usuario = $_SESSION['id_log'];
$rol_usuario = $_SESSION['rol'];

try {
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'listar':
            $filtro_tipo = $_GET['tipo'] ?? '';
            $filtro_estado = $_GET['estado'] ?? '';
            $params = [];
            $sql = "SELECT id_pqrsf, tipo, descripcion, fecha_creacion, estado, destinatario FROM tab_pqrsf";
            $where = [];

            if ($rol_usuario !== 'admin') {
                $where[] = "usuario_id = :usuario_id";
                $params[':usuario_id'] = $id_usuario;
            }

            if (!empty($filtro_tipo)) {
                $where[] = "tipo = :tipo";
                $params[':tipo'] = $filtro_tipo;
            }
            if (!empty($filtro_estado)) {
                $where[] = "estado = :estado";
                $params[':estado'] = $filtro_estado;
            }

            if (!empty($where)) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }

            $sql .= " ORDER BY fecha_creacion DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($datos);
            break;

        case 'ver_detalles':
            $id_pqrsf = intval($_GET['id'] ?? 0);
            $sql = "SELECT * FROM tab_pqrsf WHERE id_pqrsf = :id_pqrsf";
            if ($rol_usuario !== 'admin') {
                $sql .= " AND usuario_id = :usuario_id";
            }
            $stmt = $conn->prepare($sql);
            $params = [':id_pqrsf' => $id_pqrsf];
            if ($rol_usuario !== 'admin') {
                $params[':usuario_id'] = $id_usuario;
            }
            $stmt->execute($params);
            $pqrsf = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($pqrsf) {
                echo json_encode($pqrsf);
            } else {
                echo json_encode(['ok' => false, 'error' => 'PQRSF no encontrada o no tiene permiso para verla.']);
            }
            break;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $usuario_id = $_SESSION['user_id'];
    $tipo = $data['tipo'] ?? '';
    $descripcion = $data['descripcion'] ?? '';
    $contacto_solicitante = $data['contacto_solicitante'] ?? '';
    $nombre_solicitante = $data['nombre_solicitante'] ?? '';
    $destinatario = $data['destinatario'] ?? '';

    if (empty($tipo) || empty($descripcion) || empty($contacto_solicitante) || empty($nombre_solicitante) || empty($destinatario)) {
        echo json_encode(['ok' => false, 'error' => 'Todos los campos son obligatorios.']);
        exit;
    }

    $sql = "INSERT INTO tab_pqrsf (usuario_id, tipo, descripcion, contacto_solicitante, nombre_solicitante, destinatario, estado, fecha_creacion) VALUES (:usuario_id, :tipo, :descripcion, :contacto_solicitante, :nombre_solicitante, :destinatario, 'pendiente', NOW())";
    $stmt = $conn->prepare($sql);
    $params = [
        ':usuario_id' => $usuario_id,
        ':tipo' => $tipo,
        ':descripcion' => $descripcion,
        ':contacto_solicitante' => $contacto_solicitante,
        ':nombre_solicitante' => $nombre_solicitante,
        ':destinatario' => $destinatario
    ];
    if ($stmt->execute($params)) {
        echo json_encode(['ok' => true, 'message' => 'PQRSF registrada exitosamente.']);
    } else {
        echo json_encode(['ok' => false, 'error' => 'Error al registrar la PQRSF.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_pqrsf = $data['id_pqrsf'] ?? 0;
    $tipo = $data['tipo'] ?? '';
    $descripcion = $data['descripcion'] ?? '';
    $destinatario = $data['destinatario'] ?? '';
    $estado = $data['estado'] ?? '';

    if (empty($id_pqrsf) || empty($tipo) || empty($descripcion) || empty($destinatario) || empty($estado)) {
        echo json_encode(['ok' => false, 'error' => 'Todos los campos son obligatorios para actualizar.']);
        exit;
    }

    $sql = "UPDATE tab_pqrsf SET tipo = :tipo, descripcion = :descripcion, destinatario = :destinatario";
    $params = [
        ':tipo' => $tipo,
        ':descripcion' => $descripcion,
        ':destinatario' => $destinatario,
        ':estado' => $estado,
        ':id_pqrsf' => $id_pqrsf
    ];

    // Solo actualiza el estado si es un administrador
    if ($_SESSION['rol'] === 'administrador') {
        $sql .= ", estado = :estado";
    } else {
        unset($params[':estado']); // Eliminar el parámetro de estado si no es administrador
    }

    $sql .= " WHERE id_pqrsf = :id_pqrsf";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute($params)) {
        echo json_encode(['ok' => true, 'message' => 'PQRSF actualizada exitosamente.']);
    } else {
        echo json_encode(['ok' => false, 'error' => 'No se pudo actualizar la PQRSF o no tiene permiso.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? 0;

    if (empty($id)) {
        echo json_encode(['ok' => false, 'error' => 'ID de PQRSF no proporcionado.']);
        exit;
    }

    $sql = "DELETE FROM tab_pqrsf WHERE id_pqrsf = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo json_encode(['ok' => true, 'message' => 'PQRSF eliminada exitosamente.']);
    } else {
        echo json_encode(['ok' => false, 'error' => 'No se pudo eliminar la PQRSF o no tiene permiso.']);
    }
} else {
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
}