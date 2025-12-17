<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/conexion.php';

session_start();

$response = ['success' => false, 'message' => 'Error desconocido.', 'data' => []];

try {
    $sql = "SELECT 
                p.id_pqrsf, 
                p.tipo, 
                p.descripcion, 
                p.nombre_solicitante, 
                p.contacto_solicitante, 
                p.destinatario AS pqrsf_about_category, 
                p.usuario_id AS pqrsf_about_id, 
                p.fecha_creacion, 
                p.estado, 
                p.archivo_adjunto,
                COALESCE(pr.nombres || ' ' || pr.apellidos, e.nombres || ' ' || e.apellido1, 'N/A') as destinatario_nombre
            FROM tab_pqrsf p
            LEFT JOIN tab_profesores pr ON p.usuario_id = pr.id_profesor AND p.destinatario = 'Profesor'
                        LEFT JOIN tab_estudiante e ON p.usuario_id = e.id_ficha AND p.destinatario = 'Estudiante'
            WHERE 1=1";

    $params = [];

    // Lógica de filtrado por rol y creador_id
    if (isset($_SESSION['rol'])) {
        if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'administrativo') {
            // Admin y administrativo ven todo
        } else if ($_SESSION['rol'] === 'profesor') {
            $sql .= " AND (p.usuario_id = :usuario_id OR p.usuario_id = :usuario_id AND p.destinatario = 'Profesor')";
            $params[':usuario_id'] = $_SESSION['id_log'];
        } else if ($_SESSION['rol'] === 'estudiante') {
            $sql .= " AND (p.usuario_id = :usuario_id OR p.usuario_id = :usuario_id AND p.destinatario = 'Estudiante')";
            $params[':usuario_id'] = $_SESSION['id_log'];
        } else if ($_SESSION['rol'] === 'padre') {
            // Un padre puede ver las PQRSF que ha creado o las que están dirigidas a sus hijos
            $sql .= " AND (p.usuario_id = :usuario_id OR p.usuario_id IN (SELECT te.id_ficha FROM tab_estudiante te JOIN tab_acudiente ta ON te.id_acudiente = ta.id_acudiente WHERE ta.id_log = :id_acudiente))";
            $params[':usuario_id'] = $_SESSION['id_log'];
            $params[':id_acudiente'] = $_SESSION['id_log']; // Asumiendo que el id_log del padre es el id_acudiente
        }
    }


    // Si se pide un ID específico, ignorar otros filtros
    if (isset($_GET['id_pqrsf']) && !empty($_GET['id_pqrsf'])) {
        $sql .= " AND p.id_pqrsf = :id_pqrsf";
        // Limpiamos los params para asegurar que solo se use el id_pqrsf y el creador_id si es necesario
        $id_pqrsf = $_GET['id_pqrsf'];
        $params = [':id_pqrsf' => $id_pqrsf];
        if (isset($_SESSION['rol']) && $_SESSION['rol'] !== 'admin') {
            $params[':usuario_id'] = $_SESSION['id_log'];
        }
    } else {
        // Filtro por tipo
        if (isset($_GET['tipo']) && $_GET['tipo'] !== '') {
            $sql .= " AND p.tipo = :tipo";
            $params[':tipo'] = $_GET['tipo'];
        }

        // Filtro por estado
        if (isset($_GET['estado']) && $_GET['estado'] !== '') {
            $sql .= " AND p.estado = :estado";
            $params[':estado'] = $_GET['estado'];
        }

        // Búsqueda por texto
        if (isset($_GET['search']) && $_GET['search'] !== '') {
            $search = '%' . $_GET['search'] . '%';
            $sql .= " AND (p.descripcion ILIKE :search OR p.nombre_solicitante ILIKE :search OR COALESCE(pr.nombres || ' ' || pr.apellidos, e.nombres || ' ' || e.apellido1) ILIKE :search)";
            $params[':search'] = $search;
        }
    }

    $sql .= " ORDER BY p.fecha_creacion DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $pqrsf = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = ['success' => true, 'message' => 'PQRSF obtenidas exitosamente.', 'data' => $pqrsf];

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage() . ', SQL: ' . $sql . ', Params: ' . json_encode($params)];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>