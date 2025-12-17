<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');
require_once '../php/conexion.php';

$response = ['success' => false, 'message' => 'Error inesperado.'];
$id_profesor = $_GET['id_profesor'] ?? null;

if (!$id_profesor) {
    echo json_encode(['success' => false, 'message' => 'ID de profesor no proporcionado.']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT
            tc.id_curso,
            tc.nombre_curso,
            tg.id_seccion,
            tg.grado_numero,
            tg.letra_seccion
        FROM
            tab_cursos tc
        JOIN
            tab_grados tg ON CAST(tc.grado AS TEXT) = CAST(tg.grado_numero AS TEXT)
        JOIN
            profesor_grado pg ON tg.id_seccion = pg.id_grado
        WHERE
            pg.id_profesor = :id_profesor
        ORDER BY
            tg.grado_numero, tg.letra_seccion, tc.nombre_curso
    ");
    $stmt->execute([':id_profesor' => $id_profesor]);
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($cursos) {
        $response = ['success' => true, 'data' => $cursos];
    } else {
        $response = ['success' => false, 'message' => 'No se encontraron cursos para este profesor.'];
    }

} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
}

echo json_encode($response);
?>
