<?php
session_start();
header('Content-Type: application/json');

require_once 'conexion.php';

$response = ['success' => false, 'error' => ''];

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';

    switch ($action) {
        case 'create':
            $nombre = $input['nombre'] ?? '';
            $fecha_inicio = $input['fecha_inicio'] ?? '';
            $fecha_fin = $input['fecha_fin'] ?? '';
            $hora_inicio = $input['hora_inicio'] ?? '';
            $hora_fin = $input['hora_fin'] ?? '';
            $color = $input['color'] ?? '';
            $target_roles = implode(',', $input['target_roles'] ?? []);
            $target_ids = implode(',', $input['target_ids'] ?? []);
            $usuario_id = $_SESSION['id_log'] ?? null; // Usar el ID de sesión si existe
            $tipo_evento = $input['tipo_evento'] ?? 'general'; // Added tipo_evento

            if (!$usuario_id) {
                throw new Exception('Usuario no autenticado.');
            }

            $stmt = $pdo->prepare("INSERT INTO eventos (usuario_id, tipo_evento, nombre, fecha_inicio, fecha_fin, hora_inicio, hora_fin, color, target_roles, target_ids) VALUES (:usuario_id, :tipo_evento, :nombre, :fecha_inicio, :fecha_fin, :hora_inicio, :hora_fin, :color, :target_roles, :target_ids) RETURNING id");
            $stmt->execute([
                ':usuario_id' => $usuario_id,
                ':tipo_evento' => $tipo_evento,
                ':nombre' => $nombre,
                ':fecha_inicio' => $fecha_inicio,
                ':fecha_fin' => $fecha_fin,
                ':hora_inicio' => $hora_inicio,
                ':hora_fin' => $hora_fin,
                ':color' => $color,
                ':target_roles' => $target_roles,
                ':target_ids' => $target_ids
            ]);
            $response['success'] = true;
            $response['message'] = 'Evento creado exitosamente.';
            break;

        case 'update':
            $id = $input['id'] ?? 0;
            $nombre = $input['nombre'] ?? '';
            $fecha_inicio = $input['fecha_inicio'] ?? '';
            $fecha_fin = $input['fecha_fin'] ?? '';
            $hora_inicio = $input['hora_inicio'] ?? '';
            $hora_fin = $input['hora_fin'] ?? '';
            $color = $input['color'] ?? '';
            $target_roles = implode(',', $input['target_roles'] ?? []);
            $target_ids = implode(',', $input['target_ids'] ?? []);

            $stmt = $pdo->prepare("UPDATE eventos SET nombre = :nombre, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, hora_inicio = :hora_inicio, hora_fin = :hora_fin, color = :color, target_roles = :target_roles, target_ids = :target_ids WHERE id = :id");
            $stmt->execute([
                ':nombre' => $nombre,
                ':fecha_inicio' => $fecha_inicio,
                ':fecha_fin' => $fecha_fin,
                ':hora_inicio' => $hora_inicio,
                ':hora_fin' => $hora_fin,
                ':color' => $color,
                ':target_roles' => $target_roles,
                ':target_ids' => $target_ids,
                ':id' => $id
            ]);
            $response['success'] = true;
            $response['message'] = 'Evento actualizado exitosamente.';
            break;

        case 'delete':
            $id = $input['id'] ?? 0;
            $stmt = $pdo->prepare("DELETE FROM eventos WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $response['success'] = true;
            $response['message'] = 'Evento eliminado exitosamente.';
            break;

        default:
            // Lógica para obtener eventos filtrados
            $current_user_id = $_SESSION['id_log'] ?? null;
            $current_user_role = $_SESSION['rol'] ?? null;

            $sql = "SELECT id, usuario_id, nombre, fecha_inicio, fecha_fin, hora_inicio, hora_fin, color, target_roles, target_ids FROM eventos e";
            $params = [];
            $where_clauses = [];

            if ($current_user_role === 'admin') {
                // Admin ve todos los eventos, no se necesita WHERE clause
            } else {
                // 1. Eventos creados por el usuario actual
                if ($current_user_id) {
                    $where_clauses[] = "e.usuario_id = :current_user_id";
                    $params[':current_user_id'] = $current_user_id;
                }

                // 2. Eventos dirigidos al rol del usuario actual (general)
                if ($current_user_role) {
                    $where_clauses[] = "e.target_roles LIKE '%' || :current_user_role_like || '%' ";
                    $params[':current_user_role_like'] = $current_user_role;
                }

                // 3. Eventos dirigidos a IDs específicos (profesores, estudiantes, padres por sección)
                if ($current_user_id) {
                    if ($current_user_role === 'profesor') {
                        // Para profesores, target_ids puede contener su id_log
                        $where_clauses[] = ":current_user_id_str = ANY(string_to_array(e.target_ids, ',')::text[])";
                        $params[':current_user_id_str'] = (string)$current_user_id;
                    } elseif ($current_user_role === 'estudiante') {
                        // Para estudiantes, target_ids puede contener su id_seccion
                        $stmt_student_section = $pdo->prepare("SELECT te.id_seccion FROM login l JOIN tab_estudiante te ON l.usuario = te.no_documento WHERE l.id_log = :id_log");
                        $stmt_student_section->execute([':id_log' => $current_user_id]);
                        $student_section = $stmt_student_section->fetch(PDO::FETCH_ASSOC);
                        if ($student_section && $student_section['id_seccion']) {
                            $where_clauses[] = ":student_section_id = ANY(string_to_array(e.target_ids, ',')::text[])";
                            $params[':student_section_id'] = (string)$student_section['id_seccion'];
                        }
                    } elseif ($current_user_role === 'padre') {
                        // Para padres, target_ids puede contener el id_seccion de su(s) hijo(s)
                        $stmt_parent_sections = $pdo->prepare("
                            SELECT DISTINCT te.id_seccion
                            FROM tab_estudiante te
                            JOIN tab_acudiente ta ON te.id_acudiente = ta.id_acudiente
                            JOIN login l ON ta.no_documento = l.usuario -- Asumiendo que login.usuario guarda el no_documento del acudiente
                            WHERE l.id_log = :id_log
                        ");
                        $stmt_parent_sections->execute([':id_log' => $current_user_id]);
                        $parent_sections = $stmt_parent_sections->fetchAll(PDO::FETCH_COLUMN); // Obtener un array de IDs de sección

                        if (!empty($parent_sections)) {
                            $section_placeholders = [];
                            foreach ($parent_sections as $index => $section_id) {
                                $placeholder = ":parent_section_id_" . $index;
                                $section_placeholders[] = $placeholder . " = ANY(string_to_array(e.target_ids, ',')::text[])";
                                $params[$placeholder] = (string)$section_id;
                            }
                            $where_clauses[] = "(" . implode(" OR ", $section_placeholders) . ")";
                        }
                    }
                }

                if (!empty($where_clauses)) {
                    $sql .= " WHERE " . implode(" OR ", $where_clauses);
                } else {
                    // Si no hay criterios específicos y no es admin, no devolver eventos
                    $response['success'] = true;
                    $response['data'] = [];
                    echo json_encode($response);
                    exit;
                }
            }

            error_log("Current User ID: " . ($current_user_id ?? 'N/A') . ", Role: " . ($current_user_role ?? 'N/A'));
            error_log("Generated SQL: " . $sql);
            error_log("SQL Params: " . print_r($params, true));

            $sql .= " ORDER BY fecha_inicio ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response['success'] = true;
            $response['data'] = $events;
            break;
    }

} catch (PDOException $e) {
    $response['error'] = 'Error de base de datos: ' . $e->getMessage();
} catch (Exception $e) {
    $response['error'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>