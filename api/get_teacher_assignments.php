<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

include_once __DIR__ . '/../php/conexion.php';

$response = ['success' => false, 'data' => [], 'message' => ''];

try {
    $stmt = $pdo->query("
        SELECT
            tpc.id_profesor,
            tpc.id_grado,
            p.nombres AS profesor_nombres,
            p.apellidos AS profesor_apellidos,
            p.especialidad,
            g.grado_numero,
            g.letra_seccion,
            p.especialidad AS materia_nombre
        FROM
            profesor_grado tpc
        JOIN
            tab_profesores p ON tpc.id_profesor = p.id_profesor
        JOIN
            tab_grados g ON tpc.id_grado = g.id_seccion
        ORDER BY
            p.apellidos, p.nombres, g.grado_numero, g.letra_seccion
    ");
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['success'] = true;
    $response['data'] = $assignments;
} catch (PDOException $e) {
    $response['message'] = 'Error al obtener las asignaciones de profesores: ' . $e->getMessage();
}

echo json_encode($response);
?>