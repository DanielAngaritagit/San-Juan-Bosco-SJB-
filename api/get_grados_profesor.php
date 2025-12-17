<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
// api/get_grados_profesor.php

header('Content-Type: application/json');
require_once '../php/verificar_sesion.php';
require_once '../php/conexion.php';

// Asegurarse de que el rol sea profesor
if ($_SESSION['rol'] !== 'profesor') {
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit();
}

$response = ['success' => false, 'grados' => [], 'message' => ''];

try {
    // Obtener el id_profesor del usuario logueado. 
    $id_log = $_SESSION['id_log'];

    // Primero, encontrar el id_profesor correspondiente al id_log
    $stmt_profesor = $pdo->prepare("SELECT id_profesor FROM tab_profesores WHERE id_log = :id_log");
    $stmt_profesor->bindParam(':id_log', $id_log, PDO::PARAM_INT);
    $stmt_profesor->execute();
    $id_profesor = $stmt_profesor->fetchColumn();

    if (!$id_profesor) {
        throw new Exception("No se pudo encontrar el perfil del profesor.");
    }

    // Segundo, obtener solo los grados asignados a ese profesor
    $stmt_grados = $pdo->prepare(
        "SELECT g.id_seccion, CONCAT(g.grado_numero, ' ', g.letra_seccion) AS nombre_grado\n         FROM tab_grados g\n         INNER JOIN profesor_grado pg ON g.id_seccion = pg.id_grado\n         WHERE pg.id_profesor = :id_profesor\n         ORDER BY g.grado_numero, g.letra_seccion"
    );
    $stmt_grados->bindParam(':id_profesor', $id_profesor, PDO::PARAM_INT);
    $stmt_grados->execute();
    
    $grados = $stmt_grados->fetchAll(PDO::FETCH_ASSOC);

    if ($grados) {
        $response['success'] = true;
        $response['grados'] = $grados;
    } else {
        $response['message'] = 'No se encontraron grados asignados para este profesor.';
    }

} catch (Exception $e) {
    $response['message'] = 'Error al obtener los grados: ' . $e->getMessage();
}

echo json_encode($response);
?>
