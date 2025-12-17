<?php
require_once 'conexion.php'; // This now includes the PDO connection object $pdo

header('Content-Type: application/json');

$response = array('success' => false, 'error' => '');

try {
    // Ensure $pdo is available from conexion.php
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception("Database connection (PDO) not established.");
    }

    // Matricula Count (Total entries in tab_matriculas)
    $stmtMatricula = $pdo->prepare("SELECT COUNT(*) as count FROM tab_matriculas");
    $stmtMatricula->execute();
    $rowMatricula = $stmtMatricula->fetch(PDO::FETCH_ASSOC);
    $countMatricula = (int)$rowMatricula['count'];

    // Estudiantes de 11° Count
    $stmtEstudiantes11 = $pdo->prepare("SELECT COUNT(te.id_ficha) as count
                                        FROM tab_estudiante te
                                        JOIN tab_grados tg ON te.id_seccion = tg.id_seccion
                                        WHERE tg.grado_numero = 11");
    $stmtEstudiantes11->execute();
    $rowEstudiantes11 = $stmtEstudiantes11->fetch(PDO::FETCH_ASSOC);
    $countEstudiantes11 = (int)$rowEstudiantes11['count'];

    $response['success'] = true;
    $response['labels'] = ['Matrícula', 'Estudiantes 11vo Grado'];
    $response['datasets'] = [
        [
            'data' => [$countMatricula, $countEstudiantes11],
            'backgroundColor' => ['#1cc88a', '#f6c23e'], // Green, Yellow
            'hoverBackgroundColor' => ['#17a673', '#f4b619'],
            'borderColor' => '#ffffff', // White border for slices
            'borderWidth' => 1
        ]
    ];

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
} finally {
    $pdo = null;
}

echo json_encode($response);
?>