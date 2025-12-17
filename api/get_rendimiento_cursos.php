<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
session_start();
header('Content-Type: application/json');

require_once '../php/conexion.php';

$response = ['success' => false, 'data' => [], 'error' => ''];

if (!isset($_SESSION['id_log']) || $_SESSION['rol'] !== 'profesor') {
    $response['error'] = 'Acceso denegado. Se requiere autenticación de profesor.';
    echo json_encode($response);
    exit();
}

$id_profesor_log = $_SESSION['id_log']; // Get professor ID from session (from login table)
error_log("get_rendimiento_cursos.php: id_profesor_log from session: " . $id_profesor_log);

try {
    // First, get the actual id_profesor from the tab_profesores table using id_log
    $stmt_profesor_id = $pdo->prepare("SELECT id_profesor FROM tab_profesores WHERE id_log = :id_log");
    $stmt_profesor_id->execute([':id_log' => $id_profesor_log]);
    $profesor_data = $stmt_profesor_id->fetch(PDO::FETCH_ASSOC);

    if (!$profesor_data) {
        $response['error'] = 'Profesor no encontrado.';
        echo json_encode($response);
        exit();
    }

    $id_profesor = $profesor_data['id_profesor'];
    error_log("get_rendimiento_cursos.php: id_profesor from tab_profesores: " . $id_profesor);

    // Log the SQL query and parameters before execution
    $sql_query = "
        SELECT
            te.id_ficha AS id_estudiante,
            te.nombres AS nombre_estudiante,
            te.apellido1,
            te.apellido2,
            AVG(tc.calificacion) AS promedio_general
        FROM
            tab_calificaciones tc
        JOIN
            tab_estudiante te ON tc.id_estud = te.id_ficha
        WHERE
            tc.id_profesor = :id_profesor
        GROUP BY
            te.id_ficha, te.nombres, te.apellido1, te.apellido2
        ORDER BY
            te.nombres, te.apellido1;
    ";
    error_log("get_rendimiento_cursos.php: Main SQL Query: " . preg_replace('/\s+/', ' ', $sql_query));
    error_log("get_rendimiento_cursos.php: Main SQL Params: " . json_encode([':id_profesor' => $id_profesor]));

    $stmt = $pdo->prepare($sql_query);
    
    try {
        $stmt->execute([':id_profesor' => $id_profesor]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("get_rendimiento_cursos.php: Number of data rows returned: " . count($data));

        $response['success'] = true;
        $response['data'] = $data;
    } catch (PDOException $e) {
        $response['error'] = 'Error en la consulta principal de base de datos: ' . $e->getMessage();
        error_log("get_rendimiento_cursos.php: PDOException in main query: " . $e->getMessage());
    }

} catch (PDOException $e) {
    $response['error'] = 'Error de base de datos: ' . $e->getMessage();
} catch (Exception $e) {
    $response['error'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>