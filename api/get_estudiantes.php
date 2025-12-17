<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error desconocido.', 'data' => []];

try {
    $stmt = $pdo->query("SELECT 
        te.id_ficha, 
        te.nombres, 
        te.apellido1 AS apellidos, 
        te.telefonos AS telefono, 
        tg.grado_numero, 
        tg.letra_seccion
    FROM tab_estudiante te
    LEFT JOIN tab_grados tg ON te.id_seccion = tg.id_seccion
    ORDER BY te.apellido1, te.nombres");
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = ['success' => true, 'message' => 'Estudiantes obtenidos exitosamente.', 'data' => $estudiantes];

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>